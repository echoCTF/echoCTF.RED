<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\PlayerVpnHistory;

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
        $query->andFilterWhere(['or',
            ['=', 'vpn_remote_address', $this->vpn_remote_address],
            ['like', 'INET_NTOA(vpn_remote_address)', $this->vpn_remote_address]
        ]);
        $query->andFilterWhere(['or',
            ['=', 'vpn_local_address', $this->vpn_local_address],
            ['like', 'INET_NTOA(vpn_local_address)', $this->vpn_local_address]
        ]);
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
