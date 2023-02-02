<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\WriteupRating;

/**
 * WriteupRatingSearch represents the model behind the search form of `app\modules\activity\models\WriteupRating`.
 */
class WriteupRatingSearch extends WriteupRating
{
    public $username;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'writeup_id', 'player_id'], 'integer'],
            [['created_at', 'updated_at','username','rating'], 'safe'],
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
        $query = WriteupRating::find()->joinWith(['player', 'writeup']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        // grid filtering conditions
        $query->andFilterWhere([
            'writeup_rating.id' => $this->id,
            'writeup_id' => $this->writeup_id,
            'writeup_rating.player_id' => $this->player_id,
            'writeup_rating.rating' => $this->rating,
        ]);
        $query->andFilterWhere(['like', 'writeup_rating.created_at', $this->created_at]);
        $query->andFilterWhere(['like', 'writeup_rating.updated_at', $this->updated_at]);
        $query->andFilterWhere(['like','player.username',$this->username]);
        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'username' => [
                      'asc' => ['player.username' => SORT_ASC],
                      'desc' => ['player.username' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
