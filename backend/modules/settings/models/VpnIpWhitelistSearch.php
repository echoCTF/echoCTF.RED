<?php

namespace app\modules\settings\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * VpnIpWhitelistSearch represents the model behind the search form of `app\modules\settings\models\VpnIpWhitelist`.
 */
class VpnIpWhitelistSearch extends VpnIpWhitelist
{
  public $ipoctet;

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id'], 'integer'],
      [['ipoctet'], 'filter', 'filter' => 'trim'],
      [['ipoctet', 'description', 'created_at', 'updated_at'], 'safe'],
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
    $query = VpnIpWhitelist::find();

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
    ]);

    $this->load($params);

    if (!$this->validate()) {
      return $dataProvider;
    }

    $query->andFilterWhere([
      'id' => $this->id,
    ]);

    $query->andFilterWhere(['like', 'INET_NTOA(ip)', $this->ipoctet])
      ->andFilterWhere(['like', 'description', $this->description])
      ->andFilterWhere(['like', 'created_at', $this->created_at])
      ->andFilterWhere(['like', 'updated_at', $this->updated_at]);

    $dataProvider->setSort([
      'attributes' => array_merge(
        $dataProvider->getSort()->attributes,
        [
          'ipoctet' => [
            'asc' => ['ip' => SORT_ASC],
            'desc' => ['ip' => SORT_DESC],
          ],
        ]
      ),
    ]);

    return $dataProvider;
  }
}
