<?php

namespace app\modules\sales\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sales\models\ProductNetwork;

/**
 * ProductNetworkSearch represents the model behind the search form of `app\modules\sales\models\ProductNetwork`.
 */
class ProductNetworkSearch extends ProductNetwork
{
  public $product_name;
  public $network_name;
  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['network_id'], 'integer'],
      [['product_id', 'network_name', 'product_name'], 'filter', 'filter' => 'trim'],
      [['product_id', 'network_name', 'product_name'], 'safe'],

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
    $query = ProductNetwork::find()->joinWith(['network', 'product']);

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
      'network_id' => $this->network_id,
    ]);

    $query->andFilterWhere(['like', 'product_id', $this->product_id]);
    $query->andFilterWhere(['like', 'product.name', $this->product_name]);
    $query->andFilterWhere(['like', 'network.name', $this->network_name]);

    $dataProvider->setSort([
      'defaultOrder' => ['product_id' => SORT_ASC],
      'attributes' => array_merge(
        $dataProvider->getSort()->attributes,
        [
          'network_name' => [
            'asc' => ['network.name' => SORT_ASC],
            'desc' => ['network.name' => SORT_DESC],
          ],
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
