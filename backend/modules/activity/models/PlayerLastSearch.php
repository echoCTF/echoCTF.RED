<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\PlayerLast;
use app\components\validators\ExtendedIpValidator;

/**
 * PlayerLastSearch represents the model behind the search form of `app\modules\activity\models\PlayerLast`.
 */
class PlayerLastSearch extends PlayerLast
{
  public $username,$duplicates;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','duplicates'], 'integer'],
            [['vpn_remote_address', 'vpn_local_address', 'signup_ip','signin_ip'],'filter','filter'=>'trim'],
            [['on_pui', 'on_vpn', 'vpn_remote_address', 'vpn_local_address', 'signup_ip','signin_ip', 'username','ts'], 'safe'],
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
        $query=PlayerLast::find()->joinWith(['player']);

        // add conditions that should always apply here

        $dataProvider=new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if(!$this->validate())
        {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $this->queryFilters($query);
        $this->dataProviderSort($dataProvider);

        // grid filtering conditions

        return $dataProvider;
    }

    public function searchDuplicateSignupIps($params){
        $query=PlayerLast::find()->joinWith(['player']);
        $query->select(['player_last.*',new \yii\db\Expression('count(*) as duplicates'),new \yii\db\Expression('group_concat(username order by player.id SEPARATOR ", ") as offenders') ]);
        $query->where('signup_ip is not null');
        $query->groupBy(['signup_ip']);
        $query->having('duplicates>1');
        //$query->orderBy('signup_ip');
        $dataProvider=new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if(!$this->validate())
        {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $this->queryFilters($query);
        $this->dataProviderSort($dataProvider);
        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'duplicates' => [
                      'asc' => [new \yii\db\Expression('duplicates ASC') ],
                      'desc' => [new \yii\db\Expression('duplicates DESC') ],
                  ],
                ]
            ),
        ]);

        return $dataProvider;

    }
    private function queryFilters($query) {
        $query->andFilterWhere([
            'player_last.id' => $this->id,
        ]);
        $validator = new ExtendedIpValidator(['subnet' => true, 'expandIPv6' => false]);
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
          $query->andFilterWhere(['=',new \yii\db\Expression('(player_last.vpn_remote_address & inet_aton(:remote_netmask))',[':remote_netmask'=>$netmask]),new \yii\db\Expression('(inet_aton(:remote_ip) & inet_aton(:remote_netmask))',[':remote_ip'=>$ip,':remote_netmask'=>$netmask])]);
        }
        elseif (filter_var($this->vpn_remote_address, FILTER_VALIDATE_IP))
        {
          $query->andFilterWhere(['player_last.vpn_remote_address'=>ip2long($this->vpn_remote_address)]);
        }
        else
        {
          $query->andFilterWhere(['like', 'INET_NTOA(player_last.vpn_remote_address)', $this->vpn_remote_address]);
        }

        //$query->andFilterWhere(['=', 'player_last.vpn_remote_address', $this->vpn_remote_address]);
        //$query->orFilterWhere(['like', 'INET_NTOA(player_last.vpn_remote_address)', $this->vpn_remote_address]);

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
          $query->andFilterWhere(['=',new \yii\db\Expression('(player_last.vpn_local_address & inet_aton(:netmask))',[':netmask'=>$netmask]),new \yii\db\Expression('(inet_aton(:ip) & inet_aton(:netmask))',[':ip'=>$ip,':netmask'=>$netmask])]);
        }
        elseif (filter_var($this->vpn_local_address, FILTER_VALIDATE_IP))
        {
          $query->andFilterWhere(['vpn_local_address'=>ip2long($this->vpn_local_address)]);
        }
        else
        {
          $query->andFilterWhere(['like', 'INET_NTOA(player_last.vpn_local_address)', $this->vpn_local_address]);
        }

        //$query->andFilterWhere(['=', 'player_last.vpn_local_address', $this->vpn_local_address]);
        //$query->orFilterWhere(['like', 'INET_NTOA(player_last.vpn_local_address)', $this->vpn_local_address]);

        if ($validator->validate($this->signin_ip) !== false)
        {
          [$ip, $mask] = explode('/', $this->signin_ip, 2);
          if (filter_var($mask, FILTER_VALIDATE_IP)) {
            // Netmask style, keep as is
              $netmask = $mask;
          } else {
            // Prefix length, convert to dotted netmask
            $prefix = (int)$mask;
            $netmaskLong = (~((1 << (32 - $prefix)) - 1)) & 0xFFFFFFFF;
            $netmask = long2ip($netmaskLong);
          }
          $query->andFilterWhere(['=',new \yii\db\Expression('(player_last.signin_ip & inet_aton(:signin_netmask))',[':signin_netmask'=>$netmask]),new \yii\db\Expression('(inet_aton(:signin_ip) & inet_aton(:signin_netmask))',[':signin_ip'=>$ip,':signin_netmask'=>$netmask])]);
        }
        elseif (filter_var($this->signin_ip, FILTER_VALIDATE_IP))
        {
          $query->andFilterWhere(['signin_ip'=>ip2long($this->signin_ip)]);
        }
        else
        {
          $query->andFilterWhere(['like', 'INET_NTOA(player_last.signin_ip)', $this->signin_ip]);
        }

//        $query->andFilterWhere(['=', 'player_last.signin_ip', $this->signin_ip]);
//        $query->orFilterWhere(['like', 'INET_NTOA(player_last.signin_ip)', $this->signin_ip]);

        if ($validator->validate($this->signup_ip) !== false)
        {
          [$ip, $mask] = explode('/', $this->signup_ip, 2);
          if (filter_var($mask, FILTER_VALIDATE_IP)) {
            // Netmask style, keep as is
              $netmask = $mask;
          } else {
            // Prefix length, convert to dotted netmask
            $prefix = (int)$mask;
            $netmaskLong = (~((1 << (32 - $prefix)) - 1)) & 0xFFFFFFFF;
            $netmask = long2ip($netmaskLong);
          }
          $query->andFilterWhere(['=',new \yii\db\Expression('(player_last.signup_ip & inet_aton(:signup_netmask))',[':signup_netmask'=>$netmask]),new \yii\db\Expression('(inet_aton(:signup_ip) & inet_aton(:signup_netmask))',[':signup_ip'=>$ip,':signup_netmask'=>$netmask])]);
        }
        elseif (filter_var($this->signup_ip, FILTER_VALIDATE_IP))
        {
          $query->andFilterWhere(['signup_ip'=>ip2long($this->signup_ip)]);
        }
        else
        {
          $query->andFilterWhere(['like', 'INET_NTOA(player_last.signup_ip)', $this->signup_ip]);
        }

        //$query->andFilterWhere(['=', 'player_last.signup_ip', $this->signup_ip]);
        //$query->orFilterWhere(['like', 'INET_NTOA(player_last.signup_ip)', $this->signup_ip]);

        $query->andFilterWhere(['like', 'player.username', $this->username]);
        $query->andFilterWhere(['like', 'player_last.on_pui', $this->on_pui]);
        $query->andFilterWhere(['like', 'player_last.on_vpn', $this->on_vpn]);
        $query->andFilterWhere(['like', 'player_last.ts', $this->ts]);
    }

    private function dataProviderSort($dataProvider) {
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
    }

}
