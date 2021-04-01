<?php

namespace app\modules\nalog\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\nalog\models\Transaction;

/**
 * TransactionSearch represents the model behind the search form of `app\modules\nalog\models\Transaction`.
 */
class TransactionSearch extends Transaction
{
    public $type;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'source_id', 'tax_type', 'type'], 'integer'],
            [['date', 'currency'], 'safe'],
            [['amount'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Transaction::find()->alias('t')->joinWith('source')->andWhere([
            't.user_id' => $this->user_id,
        ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['date' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            't.source_id' => $this->source_id,
            't.currency' => $this->currency,
            't.tax_type' => $this->tax_type,
            'source.type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 't.amount', $this->amount]);
        if ($this->date) {
            list($from, $to) = explode(' - ', $this->date);
            $query->andFilterWhere(['between', 't.date', $from, $to . ' 23:59:59']);
        }

        return $dataProvider;
    }
}
