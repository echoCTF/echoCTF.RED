<?php

namespace app\modules\moderation\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\moderation\models\Abuser;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * AbuserSearch represents the model behind the search form of `app\modules\moderation\models\Abuser`.
 */
class AbuserSearch extends Abuser
{
  public $username;

  public function behaviors()
  {
    return [
      'typecast' => [
        'class' => AttributeTypecastBehavior::class,
        'typecastAfterValidate' => false,
        'typecastBeforeSave'    => false,
        'typecastAfterFind'     => false,
      ],
    ];
  }
  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'player_id', 'model_id', 'points'], 'integer'],
      [['resolved'], 'boolean'],
      [['title', 'body', 'reason', 'model', 'created_at', 'updated_at', 'username'], 'safe'],
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
    $query = Abuser::find()->joinWith(['player']);

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
      'abuser.id' => $this->id,
      'player_id' => $this->player_id,
      'model_id' => $this->model_id,
      'points' => $this->points,
      'resolved' => $this->resolved,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ]);

    $query->andFilterWhere(['like', 'title', $this->title])
      ->andFilterWhere(['like', 'body', $this->body])
      ->andFilterWhere(['like', 'reason', $this->reason])
      ->andFilterWhere(['like', 'player.username', $this->username])
      ->andFilterWhere(['like', 'model', $this->model]);

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
