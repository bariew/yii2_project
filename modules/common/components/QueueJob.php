<?php
/**
 * QueueJob class file
 */

namespace app\modules\common\components;
use SuperClosure\Serializer;
use yii\base\Model;
use yii\queue\JobInterface;

/**
 * Class QueueJob
 * @package app\modules\common\components
 */
class QueueJob extends Model implements JobInterface
{
    public $callback;
    public $data = [];

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $this->callback && call_user_func_array((new Serializer())->unserialize($this->callback), $this->data);
    }

    /**
     * @param $callback
     * @param array $data
     * @return string|null
     */
    public static function create($callback, $data = [])
    {
        return Container::queue()->push(new static([
            'callback' => (new Serializer())->serialize($callback),
            'data' => $data,
        ]));
    }
}