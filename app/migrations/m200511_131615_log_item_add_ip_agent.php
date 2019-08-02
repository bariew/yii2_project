<?php

use yii\db\Migration;

/**
 * Class m200511_131615_log_item_add_ip_agent
 */
class m200511_131615_log_item_add_ip_agent extends Migration
{
    private $table = '{{%log_item}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->table, 'ip', $this->string());
        $this->addColumn($this->table, 'user_agent', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->table, 'ip');
        $this->dropColumn($this->table, 'user_agent');
    }
}
