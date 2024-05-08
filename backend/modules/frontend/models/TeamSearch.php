<?php

namespace app\modules\frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\frontend\models\Team;

/**
 * TeamSearch represents the model behind the search form of `app\modules\frontend\models\Team`.
 */
class TeamSearch extends Team
{
  public $username;
  public $team_members;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'academic', 'owner_id','inviteonly','team_members','locked'], 'integer'],
            [['team_members'],'default','value'=>null ],
            [['name', 'description', 'recruitment', 'logo', 'token','username','ts'], 'safe'],
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
        $query=Team::find()->joinWith(['owner','teamPlayers']);

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
            'team.id' => $this->id,
            'team.academic' => $this->academic,
            'team.inviteonly' => $this->inviteonly,
            'team.locked' => $this->locked,
            'team.owner_id' => $this->owner_id,
        ]);

        $query->andFilterWhere(['like', 'team.name', $this->name])
            ->andFilterWhere(['like', 'team.ts', $this->ts])
            ->andFilterWhere(['like', 'team.description', $this->description])
            ->andFilterWhere(['like', 'player.username', $this->username])
            ->andFilterWhere(['like', 'team.logo', $this->logo])
            ->andFilterWhere(['like', 'team.token', $this->token]);
        if($this->team_members !== null ) $query->having(["=",'count(team_player.player_id)',$this->team_members]);
        $query->groupBy(['team.id']);

        $dataProvider->setSort([
          'attributes' => array_merge(
              $dataProvider->getSort()->attributes,
              [
                'username' => [
                    'asc' => ['player.username' => SORT_ASC],
                    'desc' => ['player.username' => SORT_DESC],
                ],
                'team_members' => [
                    'asc' => ['count(team_player.player_id)' => SORT_ASC],
                    'desc' => ['count(team_player.player_id)' => SORT_DESC],
                ],
              ]
          ),
        ]);
        return $dataProvider;
    }
}
