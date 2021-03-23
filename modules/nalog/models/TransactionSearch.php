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
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'user_id', 'source_id'], 'integer'],
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
        $query = Transaction::find()->andWhere([
            'user_id' => $this->user_id,
        ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['date' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'source_id' => $this->source_id,
            'currency' => $this->currency,
        ]);

        $query->andFilterWhere(['like', 'amount', $this->amount]);
        if ($this->date) {
            list($from, $to) = explode(' - ', $this->date);
            $query->andFilterWhere(['between', 'date', $from, $to . ' 23:59:59']);
        }

        return $dataProvider;
    }
}
