<?php

use yii\db\Schema;
use app\modules\i18n\models\Message;
use app\modules\i18n\models\SourceMessage;

class m140626_091530_translation_message extends \yii\db\Migration
{
    public function up()
    {
        $this->createTable(SourceMessage::tableName(), [
            'id'    => 'INTEGER PRIMARY KEY AUTO_INCREMENT',
            'category'  => 'VARCHAR(32)',
            'message'   => 'TEXT'
        ]);
        $this->createTable(Message::tableName(), [
            'id'    => 'INTEGER',
            'language'  => 'VARCHAR(16)',
            'translation'   => 'TEXT'
        ]);
        $this->addPrimaryKey('pk', Message::tableName(), 'id, language');
        $this->addForeignKey('fk_message_source_message', Message::tableName(), 'id',
            SourceMessage::tableName(), 'id', 'CASCADE', 'CASCADE');
        return true;
    }

    public function down()
    {
        $this->dropForeignKey('fk_message_source_message', Message::tableName());
        $this->dropTable(Message::tableName());
        $this->dropTable(SourceMessage::tableName());

        return true;
    }
}
