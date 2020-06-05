<?php


namespace app\modules\test;


use Yii;
use yii\base\Model;
use yii\base\ViewRenderer;
use yii\data\BaseDataProvider;
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
        $translationFile = Yii::getAlias('@app/messages/'
            . Yii::$app->language . '/' . $this->module->id . '_' . $this->id . '_' . $this->action->id .'.php');
        $props = [
            'language' => Yii::$app->language,
            'messages' => Yii::$app->session->getAllFlashes(true),
            'translations' => file_exists($translationFile) ? require $translationFile : [],
            '_csrf' => Yii::$app->request->csrfToken,
        ];
        foreach ($params as $name => $value) {
            if ($value instanceof Model) {
                $props[$name] = array_merge($value->toArray(), [
                    'formName' => $value->formName(),
                    'errors' => $value->errors,
                    'rules' => array_filter($value->rules(), function ($rule) {
                        return !array_filter($rule, function ($v) { return is_callable($v); }); // remove callable validators
                    }),
                ]);
            } else if ($value instanceof BaseDataProvider) {
                $props[$name] = [
                    'models' => array_map(function ($v) {
                        return $v instanceof Model ? $v->toArray() : $v;
                    }, $value->models),
                    'totalCount' => $value->totalCount,
                    'page' => $value->pagination->page,
                    'pageSize' => $value->pagination->pageSize,
                    'defaultOrder' => $value->sort->defaultOrder
                ];
            } elseif (is_array($value)) {
                $props[$name] =  array_map(function ($v) {
                    return $v instanceof Model ? $v->toArray() : $v;
                }, $value);
            } else {
                $props[$name] = $value;
            }
        }
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $props;
        }
        $content = ReactRenderer::widget([
            'componentsSourceJs' => $this->viewPath . DIRECTORY_SEPARATOR . $view . '.js',
            'component' => 'Main',
            'props' => $props,
        ]);
        return $content;
    }
}