<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DbMenuSearch represents the model behind the search form of `app\models\DbMenu`.
 */
class DbMenuSearch extends DbMenu
{
  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['label', 'url', 'visibility'], 'safe'],
      [['url', 'visibility'], 'string', 'max' => 255],
      [['parent_id', 'sort_order'], 'integer'],
      ['enabled', 'boolean'],
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
    $query = DbMenu::find();

    // add conditions that should always apply here

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => [
        'defaultOrder' => [
          'parent_id' => SORT_ASC,
          'sort_order' => SORT_ASC,
          'label'=>SORT_ASC,
        ],
      ],
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
      'parent_id' => $this->parent_id,
      'enabled' => $this->enabled,
      'sort_order' => $this->sort_order,
    ]);
    $query->andFilterWhere(['like', 'label', $this->label]);
    $query->andFilterWhere(['like', 'url', $this->label]);
    if ($this->visibility) {
      $values = explode(',', $this->visibility);
      $conditions = [];
      $params = [];
      foreach ($values as $i => $val) {
        $conditions[] = "FIND_IN_SET(:val{$i}, `visibility`) > 0";
        $params[":val{$i}"] = trim($val);
      }
      $query->andWhere(implode(' OR ', $conditions), $params);
    }


    return $dataProvider;
  }
}
