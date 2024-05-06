<?php

namespace app\modules\frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\frontend\models\Player;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * PlayerSearch represents the model behind the search form of `app\modules\frontend\models\Player`.
 */
class PlayerSearch extends Player
{
  public $on_pui, $on_vpn, $vpn_local_address,$affiliation;
  public function behaviors()
  {
    return [
      'typecast' => [
          'class' => AttributeTypecastBehavior::class,
          'attributeTypes' => [
              'academic' => AttributeTypecastBehavior::TYPE_INTEGER,
              'active' => AttributeTypecastBehavior::TYPE_INTEGER,
              'status' => AttributeTypecastBehavior::TYPE_INTEGER,
          ],
          'typecastAfterValidate' => false,
          'typecastBeforeSave' => true,
          'typecastAfterFind' => true,
      ],
      [
          'class' => TimestampBehavior::class,
          'createdAtAttribute' => 'created',
          'updatedAtAttribute' => 'ts',
          'value' => new Expression('NOW()'),
      ],
    ];
  }
  /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'academic', 'status', 'active','approval'], 'integer'],
            [['affiliation','created','vpn_local_address', 'status', 'username', 'fullname', 'email', 'type', 'password', 'activkey', 'ts', 'last_seen', 'online', 'ovpn', 'on_pui', 'on_vpn'], 'safe'],
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
        $query=Player::find()->joinWith(['last','metadata']);
        // add conditions that should always apply here

        $dataProvider=new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if(!$this->validate())
        {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'player.id' => $this->id]);
        $query->orFilterWhere([
            'player.active' => $this->active,
            'player.academic' => $this->academic,
            'player.status' => $this->status,
            'player.approval' => $this->approval,
            'player_last.on_pui' => $this->on_pui,
            'player_last.on_vpn' => $this->on_vpn,
            'player.ts' => $this->ts,
        ]);

        $query->andFilterWhere(['like', 'player.username', $this->username])
            ->andFilterWhere(['like', 'player.fullname', $this->fullname])
            ->andFilterWhere(['like', 'player.email', $this->email])
            ->andFilterWhere(['like', 'player.type', $this->type])
            ->andFilterWhere(['like', 'player.created', $this->created])
            ->andFilterWhere(['like', 'player.password', $this->password])
            ->andFilterWhere(['like', 'player.activkey', $this->activkey])
            ->andFilterWhere(['like', 'player_metadata.affiliation', $this->affiliation])
            ->andFilterWhere(['like', 'INET_NTOA(player_last.vpn_local_address)', $this->vpn_local_address]);

//        if(!empty($this->ovpn)) $query->andHaving(['like', 'ovpn', $this->ovpn]);
//        if($this->last_seen !== "" && $this->last_seen !== NULL)$query->andHaving(['like', 'last_seen', $this->last_seen]);
        if($this->online === "1") $query->andHaving(['>', 'ifnull(online,0)', $this->online]);
        else if($this->online === "0") $query->andHaving(['=', 'ifnull(online,0)', $this->online]);
        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'id' => [
                      'asc' => ['player.id' => SORT_ASC],
                      'desc' => ['player.id' => SORT_DESC],
                  ],
                  'online' => [
                      'asc' => ['online' => SORT_ASC],
                      'desc' => ['online' => SORT_DESC],
                  ],
                  'on_pui' => [
                      'asc' => ['player_last.on_pui' => SORT_ASC],
                      'desc' => ['player_last.on_pui' => SORT_DESC],
                  ],
                  'on_vpn' => [
                      'asc' => ['player_last.on_vpn' => SORT_ASC],
                      'desc' => ['player_last.on_vpn' => SORT_DESC],
                  ],
                  'vpn_local_address' => [
                      'asc' => ['player_last.vpn_local_address' => SORT_ASC],
                      'desc' => ['player_last.vpn_local_address' => SORT_DESC],
                  ],
                  'affiliation' => [
                    'asc' => ['player_metadata.affiliation' => SORT_ASC],
                    'desc' => ['player_metadata.affiliation' => SORT_DESC],
                  ],

                ]
            ),
        ]);

        return $dataProvider;
    }

}
