<?php

namespace app\modules\frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\frontend\models\TeamAudit;

/**
 * TeamAuditSearch represents the model behind the search form of `app\modules\frontend\models\TeamAudit`.
 */
class TeamAuditSearch extends TeamAudit
{
  public $team_name;
  public $player_username;

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'team_id', 'player_id'], 'integer'],
      [['action', 'message', 'team_name', 'player_username', 'ts'], 'safe'],
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
    $query = TeamAudit::find()->joinWith(['team', 'player']);

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
      'id' => $this->id,
      'team_id' => $this->team_id,
      'player_id' => $this->player_id,
      'ts' => $this->ts,
    ]);

    $query->andFilterWhere(['like', 'action', $this->action])
      ->andFilterWhere(['like', 'message', $this->message])
      ->andFilterWhere(['like', 'player.username', $this->player_username])
      ->andFilterWhere(['like', 'team.name', $this->team_name]);

    $dataProvider->setSort([
      'attributes' => array_merge(
        $dataProvider->getSort()->attributes,
        [
          'player_username' => [
            'asc' => ['player.username' => SORT_ASC],
            'desc' => ['player.username' => SORT_DESC],
          ],
          'team_name' => [
            'asc' => ['team.name' => SORT_ASC],
            'desc' => ['team.name' => SORT_DESC],
          ],
        ]
      ),
    ]);

    return $dataProvider;
  }
}
