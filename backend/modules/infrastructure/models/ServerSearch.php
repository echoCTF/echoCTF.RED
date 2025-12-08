<?php

namespace app\modules\infrastructure\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\infrastructure\models\Server;

/**
 * ServerSearch represents the model behind the search form of `app\modules\infrastructure\models\Server`.
 */
class ServerSearch extends Server
{
    /**
     * {@inheritdoc}
     */
  public function rules()
  {
      return [
          [['id', 'ip','timeout'], 'integer'],
          [['ssl'], 'boolean'],
          [['ipoctet'],'filter','filter'=>'trim'],
          [['name', 'description', 'service', 'connstr','ipoctet','network','provider_id','ipoctet'], 'safe'],
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
      $query = Server::find();

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
          'id' => $this->id,
          'timeout'=>$this->timeout,
          'ssl'=>$this->ssl,
      ]);

      $query->andFilterWhere(['like', 'name', $this->name])
          ->andFilterWhere(['like', 'network', $this->network])
          ->andFilterWhere(['like', 'description', $this->description])
          ->andFilterWhere(['like', 'service', $this->service])
          ->andFilterWhere(['like', 'connstr', $this->connstr])
          ->andFilterWhere(['like', 'provider_id', $this->provider_id]);
      $validator = new \app\components\validators\ExtendedIpValidator(['subnet' => true, 'expandIPv6' => false]);
    if ($validator->validate($this->ipoctet) !== false) {
      [$ip, $mask] = explode('/', $this->ipoctet, 2);
      if (filter_var($mask, FILTER_VALIDATE_IP)) {
        // Netmask style, keep as is
          $netmask = $mask;
      } else {
        // Prefix length, convert to dotted netmask
        $prefix = (int)$mask;
        $netmaskLong = (~((1 << (32 - $prefix)) - 1)) & 0xFFFFFFFF;
        $netmask = long2ip($netmaskLong);
      }
      $query->andFilterWhere(['=',new \yii\db\Expression('(ip & inet_aton(:remote_netmask))', [':remote_netmask'=>$netmask]),new \yii\db\Expression('(inet_aton(:remote_ip) & inet_aton(:remote_netmask))', [':remote_ip'=>$ip,':remote_netmask'=>$netmask])]);
    } elseif (filter_var($this->ipoctet, FILTER_VALIDATE_IP)) {
      $query->andFilterWhere(['ip'=>ip2long($this->ipoctet)]);
    } else {
      $query->andFilterWhere(['like', 'INET_NTOA(ip)', $this->ipoctet]);
    }

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
