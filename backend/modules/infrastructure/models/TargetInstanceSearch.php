<?php

namespace app\modules\infrastructure\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\infrastructure\models\TargetInstance;
use yii\behaviors\AttributeTypecastBehavior;
use yii\db\Expression;

/**
 * TargetInstanceSearch represents the model behind the search form of `app\modules\infrastructure\models\TargetInstance`.
 */
class TargetInstanceSearch extends TargetInstance
{
  public $username;
  public $targetname;

  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return [
      'typecast' => [
        'class' => AttributeTypecastBehavior::class,
        'typecastAfterValidate' => false,
        'typecastBeforeSave' => false,
        'typecastAfterFind' => false,
      ],
    ];
  }

  public function rules()
  {
    return [
      [['reboot', 'team_allowed'], 'integer'],
      [['ipoctet'], 'filter', 'filter' => 'trim'],
      [['created_at', 'updated_at', 'ipoctet', 'player_id', 'target_id', 'server_id', "username",'targetname'], 'safe'],
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
    $query = TargetInstance::find()->joinWith(['target', 'player', 'server']);

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
      'reboot' => $this->reboot,
      'team_allowed' => $this->team_allowed,
    ]);
    $query->andFilterWhere(['like', 'target_instance.created_at', $this->created_at])
      ->andFilterWhere(['like', 'target_instance.updated_at', $this->updated_at]);
    $query->andFilterWhere([
      'OR',
      ['LIKE', 'target.name', $this->targetname],
      ['LIKE', 'player.username', $this->username],
      ['player_id' => $this->player_id],
      ['LIKE', 'server.id', $this->server_id],
      ['server_id' => $this->server_id],
    ]);
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
      $query->andFilterWhere(['=', new \yii\db\Expression('(target_instance.ip & inet_aton(:remote_netmask))', [':remote_netmask' => $netmask]), new \yii\db\Expression('(inet_aton(:remote_ip) & inet_aton(:remote_netmask))', [':remote_ip' => $ip, ':remote_netmask' => $netmask])]);
    } elseif (filter_var($this->ipoctet, FILTER_VALIDATE_IP)) {
      $query->andFilterWhere(['target_instance.ip' => ip2long($this->ipoctet)]);
    } else {
      $query->andFilterWhere(['like', 'INET_NTOA(target_instance.ip)', $this->ipoctet]);
    }

    $dataProvider->setSort([
      'attributes' => array_merge(
        $dataProvider->getSort()->attributes,
        [
          'ipoctet' => [
            'asc' => ['ip' => SORT_ASC],
            'desc' => ['ip' => SORT_DESC],
          ],
          'username' => [
            'asc' =>  ['player.username' => SORT_ASC],
            'desc' => ['player.username' => SORT_DESC],
          ],
          'targetname' => [
            'asc' =>  ['target.name' => SORT_ASC],
            'desc' => ['target.name' => SORT_DESC],
          ],
          'server_id' => [
            'asc' =>  ['server.name' => SORT_ASC],
            'desc' => ['server.name' => SORT_DESC],
          ],

        ]
      ),
    ]);

    return $dataProvider;
  }
}
