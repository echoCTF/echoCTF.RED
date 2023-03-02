<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\PlayerScoreMonthly;

/**
 * PlayerScoreMonthlySearch represents the model behind the search form of `app\modules\activity\models\PlayerScoreMonthly`.
 */
class PlayerScoreMonthlySearch extends PlayerScoreMonthly
{
    public $username;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'points'], 'integer'],
            [['dated_at', 'ts', 'username'], 'safe'],
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
        $query = PlayerScoreMonthly::find()->joinWith(['player']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'player_id' => $this->player_id,
            'points' => $this->points,
        ])
        ->andFilterWhere(['like','player_score_monthly.ts',$this->ts])
        ->andFilterWhere(['like','player.username',$this->username])
        ->andFilterWhere(['like', 'dated_at', $this->dated_at]);
        $dataProvider->setSort([
            'defaultOrder' => ['dated_at'=>SORT_DESC,'player_id'=>SORT_ASC, 'points'=>SORT_ASC],
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
