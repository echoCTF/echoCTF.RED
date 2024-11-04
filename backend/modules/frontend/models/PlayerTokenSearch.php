<?php

namespace app\modules\frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\frontend\models\PlayerToken;

/**
 * PlayerTokenSearch represents the model behind the search form of `app\modules\frontend\models\PlayerToken`.
 */
class PlayerTokenSearch extends PlayerToken
{
  public $username;
  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['player_id', 'type', 'token', 'expires_at', 'created_at', 'username', 'description'], 'safe'],
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
    $query = PlayerToken::find()->joinWith(['player']);

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
      'expires_at' => $this->expires_at,
      'created_at' => $this->created_at,
    ]);

    $query->andFilterWhere(['like', 'player_token.type', $this->type])
      ->andFilterWhere(['like', 'token', $this->token])
      ->andFilterWhere(['like', 'description', $this->description])
      ->andFilterWhere(['like', 'player.username', $this->username]);
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
