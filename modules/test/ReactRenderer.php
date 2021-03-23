<?php


namespace app\modules\test;


use Yii;
use yii\base\Event;
use yii\base\Model;
use yii\base\ViewRenderer;
use yii\base\Widget;
use yii\data\BaseDataProvider;
use yii\db\ActiveRecord;
use yii\web\Response;

class ReactRenderer extends ViewRenderer
{
    /**
     * @param $view
     * @param $file
     * @param array $params
     * @return array|string
     * @throws \yii\base\InvalidConfigException
     */
    public function render($view, $file, $params)
    {
        preg_match_all('/\{this\.props\.translations\.(\w+\.\w+)\}/', file_get_contents($file), $keys);
        foreach ($keys[1] as $key) {
            list($category, $message) = explode('.', $key);
            $translations[$category][$message] = Yii::$app->i18n->translate($category, $message, [], Yii::$app->language);
        }
        $props = [
            '_csrf' => Yii::$app->request->csrfToken,
            'messages' => Yii::$app->session->getAllFlashes(true),
            'translations' => $translations ?? [],
        ];
        foreach ($params as $name => $value) {
            if ($value instanceof Model) {
                $props[$name] = array_merge($this->toArray($value), [
                    'formName' => $value->formName(),
                    'errors' => $value->errors,
                    'rules' => array_filter($value->rules(), function ($rule) {
                        return !array_filter($rule, function ($v) { return is_callable($v); }); // remove callable validators
                    }),
                ]);
            } else if ($value instanceof BaseDataProvider) {
                $props[$name] = [
                    'models' => array_map(function ($v) {
                        return $v instanceof Model ? $this->toArray($v) : $v;
                    }, $value->models),
                    'totalCount' => $value->totalCount,
                    'page' => $value->pagination->page,
                    'pageSize' => $value->pagination->pageSize,
                    'defaultOrder' => $value->sort->defaultOrder
                ];
            } elseif (is_array($value)) {
                $props[$name] =  array_map(function ($v) {
                    return $v instanceof Model ? $this->toArray($v) : $v;
                }, $value);
            } else {
                $props[$name] = $value;
            }
        }
        if (Yii::$app->request->isAjax || Yii::$app->request->get('is_api')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $props['translations']['test']['page_title'] = 123123;
            Yii::$app->controller->layout = false;
            return $props;
        }
        Event::on(\bTokman\react\widgets\ReactRenderer::class, Widget::EVENT_BEFORE_RUN, function () { ob_clean(); });
        return \bTokman\react\widgets\ReactRenderer::widget([
            'componentsSourceJs' => $file,
            'component' => 'Main',
            'props' => $props,
            'useTranspiler' => (pathinfo($file, PATHINFO_EXTENSION) == 'jsx'),
        ]);
    }

    /**
     * @param Model $model
     * @return array
     */
    private function toArray(Model $model)
    {
        return [
            'attributes' => $model->toArray(),
            'attributeLabels' => $model->attributeLabels(),
            'relations' => $model instanceof ActiveRecord
                ? array_map(function ($v) { return static::toArray($v); }, $model->getRelatedRecords())
                : []
        ];
    }
}