<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PlayerTargetTrafficSearch represents the model behind the search form of `PlayerTargetTraffic`.
 */
class PlayerTargetTrafficSearch extends PlayerTargetTraffic
{
  public $username;
  public $target_name;

  public function rules()
  {
    return [
      [['player_id', 'target_id', 'packets_sent', 'packets_received', 'bytes_sent', 'bytes_received'], 'integer'],
      [['username', 'target_name'], 'filter', 'filter' => 'trim'],
      [['username', 'target_name', 'first_seen', 'last_seen'], 'safe'],
    ];
  }

  public function scenarios()
  {
    return Model::scenarios();
  }

  public function attributes()
  {
    return array_merge(parent::attributes(), ['username', 'target_name']);
  }

  /**
   * @param array $params
   * @param string|null $formName
   * @return ActiveDataProvider
   */
  public function search($params, $formName = null)
  {
    $query = PlayerTargetTraffic::find()->joinWith(['player', 'target']);

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => ['defaultOrder' => ['last_seen' => SORT_DESC]],
    ]);

    $this->load($params, $formName);

    if (!$this->validate()) {
      return $dataProvider;
    }

    $query->andFilterWhere([
      'player_target_traffic.player_id' => $this->player_id,
      'player_target_traffic.target_id' => $this->target_id,
      'player_target_traffic.packets_sent' => $this->packets_sent,
      'player_target_traffic.packets_received' => $this->packets_received,
      'player_target_traffic.bytes_sent' => $this->bytes_sent,
      'player_target_traffic.bytes_received' => $this->bytes_received,
    ]);

    $query->andFilterWhere(['like', 'player.username', $this->username]);
    $query->andFilterWhere(['like', 'target.name', $this->target_name]);
    $query->andFilterWhere(['like', 'player_target_traffic.first_seen', $this->first_seen]);
    $query->andFilterWhere(['like', 'player_target_traffic.last_seen', $this->last_seen]);

    $dataProvider->setSort([
      'defaultOrder' => ['last_seen' => SORT_DESC],
      'attributes' => array_merge(
        $dataProvider->getSort()->attributes,
        [
          'username' => [
            'asc' => ['player.username' => SORT_ASC],
            'desc' => ['player.username' => SORT_DESC],
          ],
          'target_name' => [
            'asc' => ['target.name' => SORT_ASC],
            'desc' => ['target.name' => SORT_DESC],
          ],
        ]
      ),
    ]);

    return $dataProvider;
  }
}
