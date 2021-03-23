<?php
namespace app\modules\rbac\models\search;
use Yii;
use yii\data\ActiveDataProvider;
use app\modules\rbac\models\AuthItemChild;
/**
 * AuthItemChildSearch represents the model behind the search form about `app\modules\rbac\models\AuthItemChild`.
 */
class AuthItemChildSearch extends AuthItemChild
{
    public function rules()
    {
        return [
            [['parent', 'child'], 'safe'],
        ];
    }
    public function search($params)
    {
        $query = AuthItemChild::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'parent', $this->parent])
            ->andFilterWhere(['like', 'child', $this->child]);
        return $dataProvider;
    }
}