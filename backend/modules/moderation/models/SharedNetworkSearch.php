<?php

namespace app\modules\moderation\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\moderation\models\SharedNetwork;

/**
 * SharedNetworkSearch represents the model behind the search form of
 * `app\modules\moderation\models\SharedNetwork`.
 */
class SharedNetworkSearch extends SharedNetwork
{
  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'network', 'mask'], 'integer'],
      [['cidr', 'description', 'created_at'], 'safe'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function scenarios()
  {
    // bypass scenarios() implementation in the parent model
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
    $query = SharedNetwork::find();

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => [
        'defaultOrder' => ['network' => SORT_ASC],
        'attributes' => array_merge(
          ['network', 'mask', 'description', 'created_at'],
          [
            'cidr' => [
              'asc' => ['network' => SORT_ASC],
              'desc' => ['network' => SORT_DESC],
            ],
          ]
        ),
      ],
    ]);

    $this->load($params, $formName);

    if (!$this->validate())
    {
      // uncomment the following line if you do not want to return any records when validation fails
      // $query->where('0=1');
      return $dataProvider;
    }

    $query->andFilterWhere([
      'shared_network.id' => $this->id,
    ]);

    $query->andFilterWhere(['like', 'shared_network.description', $this->description]);

    if (trim($this->cidr) !== '')
    {
      $tmp = new SharedNetwork();
      $tmp->cidr = $this->cidr;
      $tmp->parseCidr('cidr', null);
      if (!$tmp->hasErrors())
      {
        // List the entries that cover the searched IP or range
        $query->andFilterWhere(
          '(shared_network.network & :snmask) = :snnetwork',
          [':snmask' => $tmp->mask, ':snnetwork' => $tmp->network & $tmp->mask]
        );
      }
      else
      {
        $query->andFilterWhere(['like', 'INET_NTOA(shared_network.network)', $this->cidr]);
      }
    }

    return $dataProvider;
  }
}
