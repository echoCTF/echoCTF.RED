<?php

namespace app\modules\infrastructure\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\infrastructure\models\PrivateNetwork;

/**
 * PrivateNetworkSearch represents the model behind the search form of `app\modules\infrastructure\models\PrivateNetwork`.
 */
class PrivateNetworkSearch extends PrivateNetwork
{
  public $username;

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'player_id', 'team_accessible'], 'integer'],
      [['username', 'name'], 'filter', 'filter' => 'trim'],
      [['name', 'created_at', 'username'], 'safe'],
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
   * @param string|null $formName Form name to be used into `->load()` method.
   *
   * @return ActiveDataProvider
   */
  public function search($params, $formName = null)
  {
    $query = PrivateNetwork::find()->joinWith(['player']);

    // add conditions that should always apply here

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
    ]);

    $this->load($params, $formName);

    if (!$this->validate()) {
      // uncomment the following line if you do not want to return any records when validation fails
      // $query->where('0=1');
      return $dataProvider;
    }

    // grid filtering conditions
    $query->andFilterWhere([
      'id' => $this->id,
      'player_id' => $this->player_id,
      'team_accessible' => $this->team_accessible,
    ]);

    $query->andFilterWhere(['like', 'name', $this->name]);
    $query->andFilterWhere(['like', 'username', $this->username]);
    $query->andFilterWhere(['like', 'created_at', $this->created_at]);

    $dataProvider->setSort([
      'attributes' => array_merge(
          $dataProvider->getSort()->attributes,
          [
          'username' => [
            'asc' =>  ['player.username' => SORT_ASC],
            'desc' => ['player.username' => SORT_DESC],
          ],
          ]
      ),
    ]);

    return $dataProvider;
  }
}
