<?php
/**
 * MessageSearch class file.
 * @copyright (c) 2015, Pavel Bariev
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace app\modules\i18n\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * Description.
 *
 * Usage:
 * @author Pavel Bariev <bariew@yandex.ru>
 *
 */
class SourceMessageSearch extends SourceMessage
{
    public $translation;
    public $language;
    public $translationUpdate;
    public $message_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['language', 'translation', 'message', 'category'], 'string'],
            [['translationUpdate'], 'safe'],
        ];
    }

    /**
     * Default index search method
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = static::find()->alias('t')
        ->select(['t.*', 'translation' => new Expression('ANY_VALUE(messages.translation)'),
            'language' =>  new Expression('ANY_VALUE(messages.language)')]);
        $query->joinWith('messages')->groupBy(['t.id']);
        
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        $dataProvider->getSort()->attributes['language'] = [
            'asc' => ['messages.language' => SORT_ASC],
            'desc' => ['messages.language' => SORT_DESC],
        ];
        $dataProvider->getSort()->attributes['translation'] = [
            'asc' => ['messages.translation' => SORT_ASC],
            'desc' => ['messages.translation' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if ($this->translation) {
            $query->andWhere(['OR',
                ['like', 'translation', $this->translation],
                ['like', 'message', $this->translation],
            ]);
        }
        if ($this->message) {
            $query->andWhere(['OR',
                ['like', 'translation', $this->message],
                ['like', 'message', $this->message],
            ]);
        }

        if ($this->translationUpdate === 'is null') {
            $query->andWhere(['OR', 'translation is null', 'translation=""']);
        }
        if ($this->translationUpdate === 'is not null') {
            $query->andWhere('translation <> ""');
        }
        $query->andFilterWhere(['language' => $this->language]);
        $query->andFilterWhere(['like', 'category', $this->category]);

        return $dataProvider;
    }
}
