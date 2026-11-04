<?php

namespace app\modules\sales\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sales\models\Product;

/**
 * ProductSearch represents the model behind the search form of `app\modules\sales\models\Product`.
 */
class ProductSearch extends Product
{
  /** @var int|null number of prices attached to the product, used for grid filtering */
  public $prices_count;

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'name', 'description', 'metadata', 'price_id', 'currency', 'created_at', 'updated_at', 'shortcode', 'htmlOptions'], 'safe'],
      [['active', 'livemode', 'weight', 'prices_count'], 'integer'],
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
    $priceCounts = Price::find()
      ->select(['product_id', 'cnt' => 'COUNT(*)'])
      ->groupBy('product_id');

    $query = Product::find()
      ->select(['product.*'])
      ->leftJoin(['pc' => $priceCounts], 'pc.product_id = product.id')
      ->with(['prices']);

    // add conditions that should always apply here

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => ['defaultOrder' => ['weight' => SORT_ASC]]
    ]);

    $dataProvider->sort->attributes['prices_count'] = [
      'asc' => ['COALESCE(pc.cnt,0)' => SORT_ASC],
      'desc' => ['COALESCE(pc.cnt,0)' => SORT_DESC],
    ];

    $this->load($params);

    if (!$this->validate()) {
      return $dataProvider;
    }

    // grid filtering conditions
    $query->andFilterWhere([
      'active' => $this->active,
      'livemode' => $this->livemode,
      'weight' => $this->weight,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
      'COALESCE(pc.cnt,0)' => $this->prices_count,
    ]);

    $query->andFilterWhere(['like', 'product.id', $this->id])
      ->andFilterWhere(['like', 'shortcode', $this->shortcode])
      ->andFilterWhere(['like', 'name', $this->name])
      ->andFilterWhere(['like', 'description', $this->description])
      ->andFilterWhere(['like', 'metadata', $this->metadata])
      ->andFilterWhere(['like', 'htmlOptions', $this->htmlOptions]);

    return $dataProvider;
  }
}
