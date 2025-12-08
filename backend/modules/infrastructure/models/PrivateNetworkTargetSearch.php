<?php

namespace app\modules\infrastructure\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\infrastructure\models\PrivateNetworkTarget;

/**
 * PrivateNetworkTargetSearch represents the model behind the search form of `app\modules\infrastructure\models\PrivateNetworkTarget`.
 */
class PrivateNetworkTargetSearch extends PrivateNetworkTarget
{
  public $target_name;
  public $server_name;
  public $private_network_name;
  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'private_network_id', 'target_id','state'], 'integer'],
      [['private_network_name', 'target_name','ipoctet'], 'filter', 'filter' => 'trim'],
      [['private_network_name', 'target_name','server_name'], 'safe'],
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
    $query = PrivateNetworkTarget::find()->joinWith(['target', 'privateNetwork','server']);

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
      'private_network_target.id' => $this->id,
      'private_network_id' => $this->private_network_id,
      'target_id' => $this->target_id,
      'server_id' => $this->server_id,
    ]);
    $query->andFilterWhere(['like', 'target.name', $this->target_name]);
    $query->andFilterWhere(['like', 'private_network.name', $this->private_network_name]);
    $query->andFilterWhere(['like', 'private_network_target.ipoctet', $this->ipoctet]);
    $query->andFilterWhere(['like', 'server.name', $this->server_name]);

    $dataProvider->setSort([
      'attributes' => array_merge(
          $dataProvider->getSort()->attributes,
          [
          'target_name' => [
            'asc' =>  ['target.name' => SORT_ASC],
            'desc' => ['target.name' => SORT_DESC],
          ],
          'server_name' => [
            'asc' =>  ['server.name' => SORT_ASC],
            'desc' => ['server.name' => SORT_DESC],
          ],
          'private_network_name' => [
            'asc' =>  ['private_network.name' => SORT_ASC],
            'desc' => ['private_network.name' => SORT_DESC],
          ],
          ]
      ),
    ]);

    return $dataProvider;
  }
}
