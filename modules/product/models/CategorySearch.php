<?php
/**
 * CategorySearch class file.
 */

namespace app\modules\product\models;

use app\modules\product\Module;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * Description.
 *
 * Usage:
 *
 */
class CategorySearch extends Category
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'lft', 'rgt', 'depth', 'is_active'], 'integer'],
            [['is_active'], 'default', 'value' => 1],
            [['title', 'name', 'content'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            static::SCENARIO_ADMIN => [
                'content', 'title', 'is_active', 'name', 'image'
            ],
            static::SCENARIO_DEFAULT => [
                'content', 'title', 'is_active', 'name', 'image'
            ]
        ];
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
        $query = (new Category())->search();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->andFilterWhere(['is_active' => $this->is_active]);
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['is_active' => $this->is_active])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
