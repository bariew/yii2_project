<?php
/**
 * Alert class file
 */

namespace app\modules\common\widgets;


use yii\base\Widget;

/**
 * Class Alert
 * @package app\modules\common\widgets
 */
class Alert extends Widget
{
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
	 * @inheritDoc
	 */
	public function run()
    {
        if ($this->type == static::TYPE_ALL) {
            foreach (\Yii::$app->session->getAllFlashes(true) as $key => $allFlash) {
                foreach ((array) $allFlash as $flash) {
                    $key = isset($this->icons[$key]) ? $key : static::TYPE_INFO;
                    echo $this->render('alert', ['text' => $flash, 'type' => $key, 'icon' => $this->icons[$key], 'options' => $this->options]);
                }
            }
            return;
        }
		if($this->text == '')
			return;

		return $this->render('alert', [
			'text' => $this->text,
			'type' => $this->type,
			'icon' => $this->icons[$this->type],
            'options' => $this->options,
		]);
	}
}
