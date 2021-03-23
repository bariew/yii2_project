<?php
/**
 * ItemSearch class file.
 */

namespace app\modules\post\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Searches post items.
 * 
 * 
 * @example
 */
class ItemSearch extends Item
{
    public $category_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'is_active'], 'integer'],
            [['title', 'brief', 'content', 'image', 'created_at'], 'safe'],
            [['is_active'], 'boolean'],
            [['user_id'], 'integer', 'on' => static::SCENARIO_ADMIN],
            [['category_id'], 'safe', 'on' => [static::SCENARIO_ADMIN, static::SCENARIO_USER]]
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
    public function search($params = [])
    {
        $query = (new Item())->search();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC]
            ]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'is_active' => $this->is_active,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'brief', $this->brief])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere([
                'like', 'DATE_FORMAT(FROM_UNIXTIME(created_at), "%Y-%m-%d")', $this->created_at
            ])
            ;
        if ($this->category_id) {
            $t = $this->tableName();
            $relation = CategoryToItem::tableName();
            $query->innerJoin($relation, "$relation.item_id = $t.id")
                ->andWhere(["$relation.category_id" => $this->category_id]);
        }

        return $dataProvider;
    }
}
