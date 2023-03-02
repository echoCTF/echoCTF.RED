<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\PlayerScore;

/**
 * PlayerScoreSearch represents the model behind the search form of `app\modules\activity\models\PlayerScore`.
 */
class PlayerScoreSearch extends PlayerScore
{
  public $player;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'points'], 'integer'],
            [['player','ts'], 'safe'],
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
        $query=PlayerScore::find()->joinWith(['player']);

        // add conditions that should always apply here

        $dataProvider=new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if(!$this->validate())
        {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'player_score.player_id' => $this->player_id,
            'player_score.points' => $this->points,
        ]);
        $query->andFilterWhere(['like', 'player.username', $this->player]);
        $query->andFilterWhere(['like', 'player_score.ts', $this->ts]);
        $dataProvider->setSort([
            'defaultOrder' => ['points'=>SORT_DESC, 'player_id'=>SORT_ASC],
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'points' => [
                    'asc' => ['points'=>SORT_ASC, 'player_id'=>SORT_ASC],
                    'desc' => ['points'=>SORT_DESC, 'player_id'=>SORT_ASC],
                  ],
                  'player' => [
                      'asc' => ['player.username' => SORT_ASC],
                      'desc' => ['player.username' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
