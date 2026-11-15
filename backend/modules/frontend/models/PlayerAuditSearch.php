<?php

namespace app\modules\frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\frontend\models\PlayerAudit;

/**
 * PlayerAuditSearch represents the model behind the search form of `app\modules\frontend\models\PlayerAudit`.
 */
class PlayerAuditSearch extends PlayerAudit
{
  public $player_username;
  public $value;

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'player_id'], 'integer'],
      [['field', 'old_value', 'new_value', 'value', 'player_username', 'ts'], 'safe'],
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
    $query = PlayerAudit::find()->joinWith(['player']);

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
    ]);

    $this->load($params);

    if (!$this->validate()) {
      return $dataProvider;
    }

    $query->andFilterWhere([
      'player_audit.id' => $this->id,
      'player_audit.player_id' => $this->player_id,
      'player_audit.field' => $this->field,
    ]);

    $query->andFilterWhere(['like', 'player_audit.old_value', $this->old_value])
      ->andFilterWhere(['like', 'player_audit.new_value', $this->new_value])
      ->andFilterWhere(['like', 'player_audit.ts', $this->ts])
      ->andFilterWhere(['like', 'player.username', $this->player_username]);

    // matches either side, eg any player that ever used a given username
    if ($this->value !== null && $this->value !== '') {
      $query->andWhere(['or', ['like', 'player_audit.old_value', $this->value], ['like', 'player_audit.new_value', $this->value]]);
    }

    $dataProvider->setSort([
      'defaultOrder' => ['id' => SORT_DESC],
      'attributes' => array_merge(
        $dataProvider->getSort()->attributes,
        [
          'player_username' => [
            'asc' => ['player.username' => SORT_ASC],
            'desc' => ['player.username' => SORT_DESC],
          ],
        ]
      ),
    ]);

    return $dataProvider;
  }
}
