<?php

namespace app\modules\comment\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\comment\models\Comment;

/**
 * SearchItem represents the model behind the search form about `app\modules\comment\models\Item`.
 */
class SearchComment extends Comment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'parent_id', 'branch_id', 'created_at', 'updated_at', 'active'], 'integer'],
            [['parent_class', 'content'], 'safe'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Comment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'parent_id' => $this->parent_id,
            'branch_id' => $this->branch_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'active' => $this->active,
        ]);

        $query->andFilterWhere(['like', 'parent_class', $this->parent_class])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
