<?php
/**
 * UserSearch class file.
 */

namespace app\modules\user\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\user\models\User;
 
/**
 * For searching users.
 * 
 * 
 * @example
 */
class UserSearch extends User
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'email', 'password', 'username', 'company_name'], 'safe', 'on' => static::SCENARIO_ROOT],
            [['status'], 'integer', 'on' => static::SCENARIO_ROOT],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Searches users.
     * @param array $params search query data
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['status' => $this->status])
            ->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'company_name', $this->company_name])
            ->andFilterWhere([
                'like', 'DATE_FORMAT(FROM_UNIXTIME(created_at), "%Y-%m-%d")', $this->created_at
            ])->andFilterWhere([
                'like', 'DATE_FORMAT(FROM_UNIXTIME(updated_at), "%Y-%m-%d")', $this->updated_at
            ])
            ;

        return $dataProvider;
    }
}
