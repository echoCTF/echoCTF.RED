<?php

namespace app\modules\target\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TargetSearch represents the model behind the search form of `app\modules\settings\models\Target`.
 */
class TargetSearch extends Target
{
  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['name'], 'string'],
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
    $query = Target::find()->orderBy(['name'=>SORT_ASC]);

    // add conditions that should always apply here
    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'pagination' => false,
      'sort'=> ['defaultOrder' => ['name' => SORT_ASC]],
    ]);

    $this->load($params);

    if (!$this->validate()) {
      return $dataProvider;
    }
    $query->andFilterWhere([
      'active' => 1,
    ]);
    $query->andFilterWhere(['like', 'name', $this->name]);
    $query->limit(5);
    return $dataProvider;
  }
}
