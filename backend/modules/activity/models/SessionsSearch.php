<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\Sessions;

/**
 * SessionsSearch represents the model behind the search form of `app\modules\activity\models\Sessions`.
 */
class SessionsSearch extends Sessions
{
  public $player;
  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'data', 'ts', 'ip', 'ipoctet', 'player'], 'safe'],
      [['expire', 'player_id', 'ip'], 'integer'],
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
    $query = Sessions::find()->joinWith(['player']);

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
      'sessions.expire' => $this->expire,
      'sessions.player_id' => $this->player_id,
      'sessions.ip' => $this->ip,
      'sessions.ts' => $this->ts,
    ]);

    $query->andFilterWhere(['like', 'sessions.id', $this->id])
      ->andFilterWhere(['like', 'INET_NTOA(ip)', $this->ipoctet])
      ->andFilterWhere(['like', 'sessions.data', $this->data]);
    $query->orFilterWhere(['like', 'player.username', $this->player]);

    $dataProvider->setSort([
      'attributes' => array_merge(
        $dataProvider->getSort()->attributes,
        [
          'ipoctet' => [
            'asc' => ['ip' => SORT_ASC],
            'desc' => ['ip' => SORT_DESC],
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
