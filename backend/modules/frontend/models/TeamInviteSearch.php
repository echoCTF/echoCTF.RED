<?php

namespace app\modules\frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\frontend\models\TeamInvite;

/**
 * TeamInviteSearch represents the model behind the search form of `app\modules\frontend\models\TeamInvite`.
 */
class TeamInviteSearch extends TeamInvite
{
  public $team;
  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'team_id'], 'integer'],
      [['token', 'created_at', 'updated_at', 'team'], 'safe'],
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
    $query = TeamInvite::find()->joinWith(['team']);

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
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ]);
    $query->andFilterWhere(['like', 'team.name', $this->team]);
    $query->andFilterWhere(['like', 'token', $this->token]);
    $dataProvider->setSort([
      'attributes' => array_merge(
        $dataProvider->getSort()->attributes,
        [
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
