<?php

namespace app\modules\settings\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use app\components\validators\ExtendedIpValidator;

/**
 * VpnIpWhitelistSearch represents the model behind the search form of `app\modules\settings\models\VpnIpWhitelist`.
 */
class VpnIpWhitelistSearch extends VpnIpWhitelist
{
  public $ipoctet;
  public $maskoctet;

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id'], 'integer'],
      [['ipoctet', 'maskoctet'], 'filter', 'filter' => 'trim'],
      [['ipoctet', 'maskoctet', 'description', 'created_at', 'updated_at'], 'safe'],
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

    $validator = new ExtendedIpValidator(['subnet' => true, 'expandIPv6' => false]);

    if ($this->ipoctet !== null && $this->ipoctet !== '') {
      if ($validator->validate($this->ipoctet)) {
        // CIDR mode, entries within the given network
        [$ip, $mask] = explode('/', $this->ipoctet, 2);
        if (filter_var($mask, FILTER_VALIDATE_IP)) {
          // Netmask style, keep as is
          $netmask = $mask;
        } else {
          // Prefix length, convert to dotted netmask
          $netmask = long2ip((~((1 << (32 - (int)$mask)) - 1)) & 0xFFFFFFFF);
        }
        $query->andWhere(['=', new Expression('(ip & INET_ATON(:netmask))', [':netmask' => $netmask]), new Expression('(INET_ATON(:ip) & INET_ATON(:netmask))', [':ip' => $ip, ':netmask' => $netmask])]);
      } elseif (filter_var($this->ipoctet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        // Single IP, entries that cover the address
        $query->andWhere(['=', new Expression('(INET_ATON(:ip) & mask)', [':ip' => $this->ipoctet]), new Expression('(ip & mask)')]);
      } else {
        $query->andFilterWhere(['like', 'INET_NTOA(ip)', $this->ipoctet]);
      }
    }

    if ($this->maskoctet !== null && $this->maskoctet !== '') {
      $maskInput = ltrim($this->maskoctet, '/');
      if (ctype_digit($maskInput) && (int)$maskInput <= 32) {
        // Prefix length, convert to the netmask
        $query->andWhere(['mask' => (~((1 << (32 - (int)$maskInput)) - 1)) & 0xFFFFFFFF]);
      } elseif (filter_var($this->maskoctet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $query->andWhere(['=', 'mask', new Expression('INET_ATON(:mask)', [':mask' => $this->maskoctet])]);
      } else {
        $query->andFilterWhere(['like', 'INET_NTOA(mask)', $this->maskoctet]);
      }
    }

    $query->andFilterWhere(['like', 'description', $this->description])
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
          'maskoctet' => [
            'asc' => ['mask' => SORT_ASC],
            'desc' => ['mask' => SORT_DESC],
          ],
        ]
      ),
    ]);

    return $dataProvider;
  }
}
