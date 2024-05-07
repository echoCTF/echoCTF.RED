<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\SpinHistory;

/**
 * SpinHistorySearch represents the model behind the search form of `app\modules\activity\models\SpinHistory`.
 */
class SpinHistorySearch extends SpinHistory
{
  public $target_name;
  public $username;

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'target_id', 'player_id'], 'integer'],
      [['created_at', 'updated_at', 'target_name', 'username'], 'safe'],
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
    $query = SpinHistory::find()->joinWith(['target', 'player']);

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
      'spin_history.id' => $this->id,
      'spin_history.target_id' => $this->target_id,
      'spin_history.player_id' => $this->player_id,
    ]);

    $query->andFilterWhere(['like', 'spin_history.created_at', $this->created_at]);
    $query->andFilterWhere(['like', 'spin_history.updated_at', $this->updated_at]);
    $query->andFilterWhere(['like', 'player.username', $this->username]);
    $query->andFilterWhere(['like', 'target.name', $this->target_name]);
    $dataProvider->setSort([
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
