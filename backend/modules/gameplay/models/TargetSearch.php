<?php

namespace app\modules\gameplay\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\gameplay\models\Target;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeTypecastBehavior;
use yii\db\Expression;

/**
 * TargetSearch represents the model behind the search form of `app\modules\gameplay\models\Target`.
 */
class TargetSearch extends Target
{
  public $headshot;
  public $network_name;
  public $pts;

  public function behaviors()
  {
    return [
      'typecast' => [
        'class' => AttributeTypecastBehavior::class,
        'typecastAfterValidate' => false,
        'typecastBeforeSave' => true,
        'typecastAfterFind' => false,
      ],
    ];
  }
  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'ip', 'timer', 'rootable', 'difficulty', 'required_xp', 'suggested_xp', 'headshot', 'weight', 'pts', 'instance_allowed', 'require_findings', 'headshot_points', 'first_headshot_points'], 'integer'],
      ['active', 'boolean'],
      [['headshot'], 'default', 'value' => null],
      [['status'], 'in', 'range' => ['online', 'offline', 'powerup', 'powerdown', 'maintenance']],
      [['scheduled_at'], 'datetime'],
      [['ipoctet'], 'filter', 'filter' => 'trim'],
      [['timer', 'name', 'fqdn', 'pts', 'purpose', 'description', 'mac', 'net', 'server', 'image', 'dns', 'parameters', 'ipoctet', 'status', 'scheduled_at', 'required_xp', 'suggested_xp', 'headshot', 'category', 'network_name'], 'safe'],
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
    $query = Target::find()->joinWith(['headshots', 'network']);
    $query->addSelect('target.*,(select sum(points) from finding where target_id=target.id) + (select sum(points) from treasure where target_id=target.id) as pts');

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
      'target.id' => $this->id,
      'ip' => $this->ip,
      'target.active' => $this->active,
      'rootable' => $this->rootable,
      'instance_allowed' => $this->instance_allowed,
      'require_findings' => $this->require_findings,
      'first_headshot_points' => $this->first_headshot_points,
      'headshot_points' => $this->headshot_points,
      'difficulty' => $this->difficulty,
      'suggested_xp' => $this->suggested_xp,
      'required_xp' => $this->required_xp,
      'status' => $this->status,
      'target.weight' => $this->weight,
      'target.timer' => $this->timer,
    ]);

    $query->andFilterWhere(['like', 'target.name', $this->name])
      ->andFilterWhere(['like', 'category', $this->category])
      ->andFilterWhere(['like', 'scheduled_at', $this->scheduled_at])
      ->andFilterWhere(['like', 'fqdn', $this->fqdn])
      ->andFilterWhere(['like', 'purpose', $this->purpose])
      ->andFilterWhere(['like', 'description', $this->description])
      ->andFilterWhere(['like', 'mac', $this->mac])
      ->andFilterWhere(['like', 'net', $this->net])
      ->andFilterWhere(['like', 'server', $this->server])
      ->andFilterWhere(['like', 'image', $this->image])
      ->andFilterWhere(['like', 'dns', $this->dns])
      ->andFilterWhere(['like', 'parameters', $this->parameters])
      ->andFilterWhere(['like', 'network.name', $this->network_name]);
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
      $query->andFilterWhere(['=', new \yii\db\Expression('(ip & inet_aton(:remote_netmask))', [':remote_netmask' => $netmask]), new \yii\db\Expression('(inet_aton(:remote_ip) & inet_aton(:remote_netmask))', [':remote_ip' => $ip, ':remote_netmask' => $netmask])]);
    } elseif (filter_var($this->ipoctet, FILTER_VALIDATE_IP)) {
      $query->andFilterWhere(['ip' => ip2long($this->ipoctet)]);
    } else {
      $query->andFilterWhere(['like', 'INET_NTOA(ip)', $this->ipoctet]);
    }

    if ($this->pts)
      $query->andHaving(['pts' => $this->pts]);
    if ($this->headshot !== null) $query->having(["=", 'count(headshot.player_id)', $this->headshot]);
    $query->groupBy(['target.id']);
    $dataProvider->setSort([
      'attributes' => array_merge(
        $dataProvider->getSort()->attributes,
        [
          'target.id' => [
            'asc' => ['target.id' => SORT_ASC],
            'desc' => ['target.id' => SORT_DESC],
          ],
          'ipoctet' => [
            'asc' => ['ip' => SORT_ASC],
            'desc' => ['ip' => SORT_DESC],
          ],
          'network_name' => [
            'asc' => ['network.name' => SORT_ASC],
            'desc' => ['network.name' => SORT_DESC],
          ],
          'headshot' => [
            'asc' => ['COUNT(headshot.player_id)' => SORT_ASC],
            'desc' => ['COUNT(headshot.player_id)' => SORT_DESC],
          ],
          'headshot_points' => [
            'asc' => ['headshot_points' => SORT_ASC],
            'desc' => ['headshot_points' => SORT_DESC],
          ],
          'first_headshot_points' => [
            'asc' => ['first_headshot_points' => SORT_ASC],
            'desc' => ['first_headshot_points' => SORT_DESC],
          ],
          'pts' => [
            'asc' => ['pts' => SORT_ASC],
            'desc' => ['pts' => SORT_DESC],
          ],
        ]
      ),
    ]);

    return $dataProvider;
  }
}
