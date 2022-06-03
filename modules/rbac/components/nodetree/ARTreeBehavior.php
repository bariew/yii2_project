<?php

namespace app\modules\rbac\components\nodetree;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class ARTreeBehavior
 * @package app\modules\rbac\components\nodetree
 * @property ActiveRecord $owner
 */
class ARTreeBehavior extends Behavior
{
    /* ATTRIBUTES AND LISTS */

    public $id          = 'id';
    public $parent_id   = 'pid';
    public $title       = 'title';
    public $rank        = 'rank';
    public $url         = 'url';
    public $slug        = 'name';
    public $content     = 'content';
    public $actionPath  = '/rbac/item/update';

    public static $uniqueKey = 0;


    /* EVENTS*/

    /**
     * @inheritDoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT   => function () { $this->beforeInsert();},
            ActiveRecord::EVENT_BEFORE_UPDATE   => function () { $this->beforeInsert();},
            ActiveRecord::EVENT_AFTER_DELETE   => function () {
                $this->treeResort($this->get('rank'), -1);
                $this->owner->deleteAll($this->getDescendantCondition());
            },
        ];
    }

    /**
     *
     */
    private function beforeInsert()
    {
        $this->set('rank', $this->getLastRank() + 1);
        $this->createUrl();
    }


    /* MENU WIDGET */

    public function menuWidget($view='node', $attributes=array(), $return=false)
    {
        $items = $this->childrenTree($attributes);
        $behavior = $this;
        $widget =  new ARTreeMenuWidget(compact('view', 'items', 'behavior'), $return);
        return $widget->run();
    }

    public function nodeAttributes($model = false, $pid = '')
    {
        $uniqueKey = self::$uniqueKey++;
        $children = (array) @$model['children'];
        $model = ($model) ? $model['model'] : $this->owner;
        $id    = $model[$this->id];
        $nodeId = $uniqueKey . '-id-' . $id;
        return array(
            'id'    => $nodeId,
            'model'  => $model,
            'children'=>$children,
            'text'  => $model['title'],
            'type'  => 'folder',
            //'li_attr'=>[],
            'a_attr'=> array(
                'data-id'   => $nodeId,
                'onclick' => "event.preventDefault(); event.stopPropagation(); var t = this; setTimeout(function(){ $(t).contextmenu();}, 100); return false;",
                'href'    => [$this->actionPath, $this->id => $id, 'pid'=>$pid]
            )
        );
    }


    /* TREE BUILD */

    public function childrenTree($conditions = array())
    {
        $items =  $this->owner->find()->where(
            array_merge($this->getDescendantCondition(), $conditions)
        )->all();
        return $this->toTree($items);
    }

    protected function toTree($items)
    {
        $id     = $this->id;
        $result = array();
        $list   = array();
        foreach($items as $item){
            $thisref = &$result[$item[$id]];
            $children = isset($result[$item[$id]]['children'])
                ? $result[$item[$id]]['children']
                : array();
            $thisref = array('model'=>$item, 'children'=>$children);
            if($item[$id] == $this->get('id')){
                $list[$item[$id]] = &$thisref;
            }else{
                $result[$item[$this->parent_id]]['children'][$item[$id]] = &$thisref;
            }
        }

        return $this->rangeTree($list);
    }

    private function rangeTree($items)
    {
        $result = array();
        foreach($items as $item){
            $item['children'] = $this->rangeTree($item['children']);
            $count = $item['model'][$this->rank];
            while(isset($result[$count])){
                $count++;
            }
            $result[$count] = $item;
        }
        ksort($result);
        return $result;
    }


    /* TREE UPDATE */

    protected function createUrl()
    {
        $oldUrl = $this->get('url');
        $newUrl = (($parent = $this->getParent()) ? $parent->{$this->url} : '/') . $this->get('slug') . "/";
        if($newUrl == $oldUrl){
            return false;
        }
        if (!$this->get('parent_id')) {
            return $this->set('slug', '');
        }
        return ($oldUrl)
            ? \Yii::$app->db->createCommand("
                UPDATE {$this->owner->tableName()}
                  SET {$this->url} = REPLACE({$this->url}, '{$oldUrl}', '{$newUrl}')
                WHERE {$this->url} LIKE '{$oldUrl}%'
            ")->execute()
            : $this->set('url', $newUrl);
    }

    private function treeResort($rank = 0, $increment = 1)
    {
        return $this->owner->updateAllCounters(
            ['rank' => $increment],
            ["AND", [$this->parent_id => $this->get('parent_id')], ['>', $this->rank, $rank]]
        );
    }

    public function treeMove($pid, $rank = false)
    {
        if ($rank === false) {
            $rank = $this->getLastRank() + 1;
        }
        $selfShift = ($pid == $this->get('parent_id')) && ($rank > $this->get('rank'));
        $this->treeResort($this->get('rank'), -1);
        $this->set('parent_id', $pid)->createUrl();
        $this->treeResort($rank - ($selfShift ? 2 : 1), 1);
        return $this->owner->updateAttributes([
            $this->parent_id => $pid,
            $this->rank      => $rank
        ]);
    }



    /* GETTERS */

    protected function getParent()
    {
        return $this->owner->findOne(array(
            $this->id => $this->get('parent_id')
        ));
    }

    protected function getDescendantCondition()
    {
        if(!$url = $this->get('url')){
            $url = '/';
        }
        return ['like', 'url', $url];
    }

    protected function get($attributeName)
    {
        return $this->owner->{$this->$attributeName};
    }

    protected function set($attributeName, $value)
    {
        $this->owner->{$this->$attributeName} = $value;
        return $this;
    }

    protected function getLastRank()
    {
        $max = $this->owner->find()->where([$this->parent_id => $this->get('parent_id')])->max("`$this->rank`");
        return is_numeric($max) ? $max : -1;
    }
}