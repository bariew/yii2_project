<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%common_settings}}`.
 */
class m240522_090946_create_common_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%common_settings}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'value' => $this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%common_settings}}');
    }
}
