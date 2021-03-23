<?php
/**
 * MainMenu class file.
 */

namespace app\modules\page\widgets;
use bariew\dropdown\Nav;
use \app\modules\page\models\Page;
use yii\helpers\Url;

/**
 * Widget for the site main menu.
 *
 */
class MainMenu extends Nav
{
    /**
     * @inheritdoc
     */
    public $activateParents = true;
    
    /**
     * @inheritdoc
     */
    public function init() 
    {
        $cssClass = @$this->options['class'];
        parent::init();// significant order
        if ($cssClass != $this->options['class']) {
            \yii\helpers\Html::removeCssClass($this->options, 'nav');
        }
        $this->items = Page::find()
            ->select(['*', 'parent_id' => '(IF(pid=1,"",pid))', 'name' => 'title'])
            ->where(['visible' => true])
            ->andWhere(['<>', 'pid', ''])
            ->indexBy('id')
            ->orderBy(['rank' => SORT_ASC])
            ->asArray()
            ->all();
    }

    /**
     * @inheritdoc
     */
    protected function isItemActive($item)
    {
        return is_numeric(strpos('/'.\Yii::$app->request->pathInfo .'/', $item['url'][0]));
    }
}
