<?php

namespace app\modules\gameplay\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\gameplay\models\Network;

/**
 * NetworkSearch represents the model behind the search form of `app\modules\gameplay\models\Network`.
 */
class NetworkSearch extends Network
{
    public $network_players;
    public $network_targets;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','weight','network_players','network_targets'], 'integer'],
            [['network_players','network_targets'],'default','value'=>null ],
            [['public', 'active','announce','guest'], 'boolean'],
            [['name', 'description', 'codename', 'icon', 'ts'], 'safe'],
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
        $query=Network::find()->joinWith(['targets','players']);

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

        // grid filtering conditions
        $query->andFilterWhere([
            'network.id' => $this->id,
            'network.weight' => $this->weight,
            'network.active' => $this->active,
            'network.announce' => $this->announce,
            'network.public' => $this->public,
            'network.guest' => $this->guest,
            'network.ts' => $this->ts,
        ]);

        $query->andFilterWhere(['like', 'network.name', $this->name])
            ->andFilterWhere(['like', 'network.codename', $this->codename])
            ->andFilterWhere(['like', 'network.description', $this->description]);
        if($this->network_players !== null ) $query->andHaving(["=",'count(DISTINCT player.id)',$this->network_players]);
        if($this->network_targets !== null ) $query->andHaving(["=",'count(DISTINCT target.id)',$this->network_targets]);
        $query->groupBy(['network.id']);
        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'network_players' => [
                      'asc' => ['count(DISTINCT player.id)' => SORT_ASC],
                      'desc' => ['count(DISTINCT player.id)' => SORT_DESC],
                  ],
                  'network_targets' => [
                    'asc' => ['count(distinct target.id)' => SORT_ASC],
                    'desc' => ['count(distinct target.id)' => SORT_DESC],
                  ],
              ]
            ),
          ]);
        return $dataProvider;
    }
}
