<?php

namespace app\modules\speedprogramming\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\speedprogramming\models\SpeedProblem;

/**
 * SpeedProblemSearch represents the model behind the search form of `app\modules\speedprogramming\models\SpeedProblem`.
 */
class SpeedProblemSearch extends SpeedProblem
{
  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id'], 'integer'],
      [['name', 'description', 'challenge_image', 'validator_image', 'server', 'created_at', 'updated_at'], 'safe'],
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
    $query = SpeedProblem::find();

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
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ]);

    $query->andFilterWhere(['like', 'name', $this->name])
      ->andFilterWhere(['like', 'description', $this->description])
      ->andFilterWhere(['like', 'server', $this->server])
      ->andFilterWhere(['like', 'challenge_image', $this->challenge_image])
      ->andFilterWhere(['like', 'validator_image', $this->validator_image]);

    return $dataProvider;
  }
}
