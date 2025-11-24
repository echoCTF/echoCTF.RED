<?php

namespace app\modules\sales\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sales\models\PlayerProduct;

/**
 * PlayerProductSearch represents the model behind the search form of `app\modules\sales\models\PlayerProduct`.
 */
class PlayerProductSearch extends PlayerProduct
{
  public $username;
  public $product_name;

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'player_id'], 'integer'],
      [['price_id', 'ending', 'metadata', 'created_at', 'updated_at', 'username', 'product_name'], 'safe'],
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
    $query = PlayerProduct::find()->joinWith(['player', 'product']);

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
      'id' => $this->id,
      'player_id' => $this->player_id,
      'ending' => $this->ending,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ]);

    $query->andFilterWhere(['like', 'price_id', $this->price_id])
      ->andFilterWhere(['like', 'player.username', $this->username])
      ->andFilterWhere(['like', 'product.name', $this->product_name])
      ->andFilterWhere(['like', 'metadata', $this->metadata]);

    $dataProvider->setSort([
      'defaultOrder' => ['ending' => SORT_ASC],
      'attributes' => array_merge(
        $dataProvider->getSort()->attributes,
        [
          'username' => [
            'asc' => ['player.username' => SORT_ASC],
            'desc' => ['player.username' => SORT_DESC],
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
