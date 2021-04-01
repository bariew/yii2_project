<?php

use yii\db\Migration;

/**
 * Class m210331_121330_create
 */
class m210331_121330_nalog_currency_history extends Migration
{
    private $table = '{{%nalog_currency_history}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->table, [
            'date' => $this->date(),
            'usd' => $this->float(4),
            'eur' => $this->float(4),
            'cny' => $this->float(4),
            'gbp' => $this->float(4),
            'gold' => $this->float(4),
            'silver' => $this->float(4),
            'platinum' => $this->float(4),
            'palladium' => $this->float(4),
        ]);
        \app\modules\common\helpers\DbHelper::createIndex($this->table, 'date', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->table);
    }
}
