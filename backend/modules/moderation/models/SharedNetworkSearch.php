<?php

namespace app\modules\moderation\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use app\modules\moderation\models\SharedNetwork;

/**
 * SharedNetworkSearch represents the model behind the search form of
 * `app\modules\moderation\models\SharedNetwork`.
 */
class SharedNetworkSearch extends SharedNetwork
{
  /**
   * {@inheritdoc}
   * The inherited typecast behavior turns empty filter inputs (eg. an
   * empty id) into 0 after validation, and since 0 is not considered
   * empty by andFilterWhere() every search would get `id = 0` attached.
   */
  public function behaviors()
  {
    $behaviors = parent::behaviors();
    unset($behaviors['typecast']);
    return $behaviors;
  }

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
      $value = trim($this->cidr);
      // Bare IPv4 addresses are searched as /32 networks so they use the
      // bitwise filter instead of falling back to the string match
      if (strpos($value, '/') === false && filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false)
        $value .= '/32';

      $tmp = new SharedNetwork();
      $tmp->cidr = $value;
      $tmp->parseCidr('cidr', null);
      if (!$tmp->hasErrors())
      {
        // List the entries whose range covers the searched IP or range
        // (same containment direction as SharedNetwork::excludeShared());
        // andWhere() since andFilterWhere() only accepts array conditions
        $query->andWhere(new Expression('(:snnetwork & shared_network.mask) = shared_network.network', [':snnetwork' => $tmp->network]));
      }
      else
      {
        $query->andFilterWhere(['like', 'INET_NTOA(shared_network.network)', $this->cidr]);
      }
    }

    return $dataProvider;
  }
}
