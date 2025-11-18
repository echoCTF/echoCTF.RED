<?php

namespace app\modules\sales\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sales\models\Price;

/**
 * PriceSearch represents the model behind the search form of `app\modules\sales\models\Price`.
 */
class PriceSearch extends Price
{
  public $product_name;

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'recurring_interval', 'metadata', 'currency', 'nickname', 'product_id', 'ptype'], 'string'],
      [['active'], 'boolean'],
      [['unit_amount', 'interval_count', 'product_name'], 'safe']
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
    $query = Price::find()->joinWith(['product']);

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
      'active' => $this->active,
      'unit_amount' => $this->active
    ]);

    $query->andFilterWhere(['like', 'id', $this->id])
      ->andFilterWhere(['like', 'product_id', $this->product_id])
      ->andFilterWhere(['like', 'product.name', $this->product_name]);

    $dataProvider->setSort([
      'defaultOrder' => ['unit_amount' => SORT_ASC, 'id' => SORT_ASC],
      'attributes' => array_merge(
        $dataProvider->getSort()->attributes,
        [
          'product_name' => [
            'asc' => ['product.name' => SORT_ASC],
            'desc' => ['product.name' => SORT_DESC],
          ],
        ]
      ),
    ]);

    return $dataProvider;
  }
}
