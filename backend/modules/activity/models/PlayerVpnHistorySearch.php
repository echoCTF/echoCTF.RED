<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\PlayerVpnHistory;
use app\components\validators\ExtendedIpValidator;

/**
* PlayerVpnHistorySearch represents the model behind the search form of `app\modules\activity\models\PlayerVpnHistory`.
*/
class PlayerVpnHistorySearch extends PlayerVpnHistory
{
  public $username;
  /**
  * {@inheritdoc}
  */
  public function rules()
  {
    return [
      [['id', 'player_id'], 'integer'],
      [['vpn_remote_address', 'vpn_local_address','username'],'filter','filter'=>'trim'],
      [['ts', 'vpn_remote_address', 'vpn_local_address', 'username'], 'safe'],
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
    $validator = new ExtendedIpValidator(['subnet' => true, 'expandIPv6' => false]);

    $query=PlayerVpnHistory::find()->joinWith(['player']);

    // add conditions that should always apply here

    $dataProvider=new ActiveDataProvider([
      'query' => $query,
      'sort'=> ['defaultOrder' => ['ts' => SORT_DESC]],
    ]);

    $this->load($params);

    if(!$this->validate())
      {
      // uncomment the following line if you do not want to return any records when validation fails
      // $query->where('0=1');
      return $dataProvider;
    }

    // grid filtering conditions
    $query->andFilterWhere([
      'player_vpn_history.id' => $this->id,
      'player_vpn_history.player_id' => $this->player_id,
    ]);

    // CIDR mode
    if ($validator->validate($this->vpn_remote_address) !== false)
    {
      [$ip, $mask] = explode('/', $this->vpn_remote_address, 2);
      if (filter_var($mask, FILTER_VALIDATE_IP)) {
        // Netmask style, keep as is
          $netmask = $mask;
      } else {
        // Prefix length, convert to dotted netmask
        $prefix = (int)$mask;
        $netmaskLong = (~((1 << (32 - $prefix)) - 1)) & 0xFFFFFFFF;
        $netmask = long2ip($netmaskLong);
      }
      $query->andFilterWhere(['=',new \yii\db\Expression('(vpn_remote_address & inet_aton(:remote_netmask))',[':remote_netmask'=>$netmask]),new \yii\db\Expression('(inet_aton(:remote_ip) & inet_aton(:remote_netmask))',[':remote_ip'=>$ip,':remote_netmask'=>$netmask])]);
    }
    elseif (filter_var($this->vpn_remote_address, FILTER_VALIDATE_IP))
    {
      $query->andFilterWhere(['vpn_remote_address'=>ip2long($this->vpn_remote_address)]);
    }
    else
    {
      $query->andFilterWhere(['like', 'INET_NTOA(vpn_remote_address)', $this->vpn_remote_address]);
    }

    if ($validator->validate($this->vpn_local_address) !== false)
    {
      [$ip, $mask] = explode('/', $this->vpn_local_address, 2);
      if (filter_var($mask, FILTER_VALIDATE_IP)) {
        // Netmask style, keep as is
          $netmask = $mask;
      } else {
        // Prefix length, convert to dotted netmask
        $prefix = (int)$mask;
        $netmaskLong = (~((1 << (32 - $prefix)) - 1)) & 0xFFFFFFFF;
        $netmask = long2ip($netmaskLong);
      }
      $query->andFilterWhere(['=',new \yii\db\Expression('(vpn_local_address & inet_aton(:netmask))',[':netmask'=>$netmask]),new \yii\db\Expression('(inet_aton(:ip) & inet_aton(:netmask))',[':ip'=>$ip,':netmask'=>$netmask])]);
    }
    elseif (filter_var($this->vpn_local_address, FILTER_VALIDATE_IP))
    {
      $query->andFilterWhere(['vpn_local_address'=>ip2long($this->vpn_local_address)]);
    }
    else
    {
      $query->andFilterWhere(['like', 'INET_NTOA(vpn_local_address)', $this->vpn_local_address]);
    }

    $query->andFilterWhere(['like', 'player.username', $this->username]);
    $query->andFilterWhere(['like', 'player_vpn_history.ts', $this->ts]);
    $dataProvider->setSort([
      'attributes' => array_merge(
        $dataProvider->getSort()->attributes,
        [
          'username' => [
            'asc' => ['player.username' => SORT_ASC],
            'desc' => ['player.username' => SORT_DESC],
          ],
        ]
      ),
    ]);

    return $dataProvider;
  }
}
