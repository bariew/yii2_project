<?php
/**
 * Alert class file
 */

namespace app\modules\common\widgets;


use app\modules\common\helpers\ArrayHelper;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Class Alert
 * @package app\modules\common\widgets
 */
class Alert extends Widget
{
    public $id = 'main_alert';
	const TYPE_SUCCESS = 'success';
	const TYPE_DANGER = 'danger';
	const TYPE_WARNING = 'warning';
	const TYPE_INFO = 'info';

	const TYPE_ALL = 'all';

	public $text;
	public $type = self::TYPE_ALL;

	public $options = [];

	public $icons = [
		self::TYPE_SUCCESS => 'check-all',
		self::TYPE_DANGER => 'block-helper',
		self::TYPE_WARNING => 'alert-outline',
		self::TYPE_INFO => 'alert-circle-outline',
	];

    /**
     *
     */
    public static function successfullySaved()
    {
        \Yii::$app->session->setFlash(Alert::TYPE_SUCCESS, \Yii::t('common', 'Successfully saved.'));
    }

    /**
	 * @inheritDoc
	 */
	public function run()
    {
        $this->registerJs();
        $result = Html::beginTag('div', ['id' => $this->id]);
        if ($this->type == static::TYPE_ALL) {
            foreach (\Yii::$app->session->getAllFlashes(true) as $key => $allFlash) {
                foreach ((array) $allFlash as $flash) {
                    $key = isset($this->icons[$key]) ? $key : static::TYPE_INFO;
                    $result .= $this->render('alert', ['text' => $flash, 'type' => $key, 'icon' => $this->icons[$key], 'options' => $this->options]);
                }
            }
        } else {
            $result .= $this->render('alert', [
                'text' => $this->text,
                'type' => $this->type,
                'icon' => $this->icons[$this->type],
                'options' => $this->options,
            ]);
        }
        return $result . Html::endTag('div');
	}

    /**
     *
     */
	public function registerJs()
    {
        $views = json_encode(ArrayHelper::mapAssoc($this->icons, function ($k, $v) {
            return $this->render('alert', ['text' => '{text}', 'type' => $k, 'icon' => $v, 'options' => []]);
        }));
        \Yii::$app->view->registerJs(<<<JS
    views = {$views};
    mainAlert = {
        add: function (type, text) {
            $('#{$this->id}').html(views[type].replace('{text}', text));
            window.scrollTo(0,0);
        },
        clear: function () {
            $('#{$this->id}').html('');
        }
    }
JS
        );
    }
}

