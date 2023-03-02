<?php

namespace app\modules\frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\frontend\models\TeamPlayer;

/**
 * TeamPlayerSearch represents the model behind the search form of `app\modules\frontend\models\TeamPlayer`.
 */
class TeamPlayerSearch extends TeamPlayer
{
    public $player;
    public $team;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'team_id', 'player_id', 'approved'], 'integer'],
            [['ts', 'player', 'team'], 'safe'],
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
        $query=TeamPlayer::find()->joinWith(['player', 'team']);

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
            'team_player.id' => $this->id,
            'team_player.team_id' => $this->team_id,
            'team_player.player_id' => $this->player_id,
            'team_player.approved' => $this->approved,
        ]);

        $query->andFilterWhere(['like', 'player.id', $this->player]);
        $query->orFilterWhere(['like', 'player.username', $this->player]);
        $query->andFilterWhere(['like', 'team.id', $this->team]);
        $query->orFilterWhere(['like', 'team.name', $this->team]);
        $query->orFilterWhere(['like', 'team_player.ts', $this->ts]);

        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'player' => [
                      'asc' => ['player.username' => SORT_ASC],
                      'desc' => ['player.username' => SORT_DESC],
                  ],
                  'team' => [
                      'asc' => ['team.name' => SORT_ASC],
                      'desc' => ['team.name' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
