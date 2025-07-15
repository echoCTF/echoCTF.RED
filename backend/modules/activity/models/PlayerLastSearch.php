<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\PlayerLast;

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
        //die(var_dump($query->createCommand()->rawSql));

        return $dataProvider;

    }
    private function queryFilters($query) {
        $query->andFilterWhere([
            'player_last.id' => $this->id,
        ]);
        $query->andFilterWhere(['=', 'player_last.vpn_remote_address', $this->vpn_remote_address]);
        $query->orFilterWhere(['like', 'INET_NTOA(player_last.vpn_remote_address)', $this->vpn_remote_address]);
        $query->andFilterWhere(['=', 'player_last.vpn_local_address', $this->vpn_local_address]);
        $query->orFilterWhere(['like', 'INET_NTOA(player_last.vpn_local_address)', $this->vpn_local_address]);

        $query->andFilterWhere(['=', 'player_last.signin_ip', $this->signin_ip]);
        $query->orFilterWhere(['like', 'INET_NTOA(player_last.signin_ip)', $this->signin_ip]);

        $query->andFilterWhere(['=', 'player_last.signup_ip', $this->signup_ip]);
        $query->orFilterWhere(['like', 'INET_NTOA(player_last.signup_ip)', $this->signup_ip]);

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
