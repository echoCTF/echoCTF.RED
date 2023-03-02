<?php

namespace app\modules\gameplay\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\gameplay\models\NetworkPlayer;

/**
 * NetworkPlayerSearch represents the model behind the search form of `app\modules\gameplay\models\NetworkPlayer`.
 */
class NetworkPlayerSearch extends NetworkPlayer
{
    public $username;
    public $network_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['network_id', 'player_id'], 'integer'],
            [['created_at', 'updated_at', 'username','network_name'], 'safe'],
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
        $query=NetworkPlayer::find()->joinWith(['player','network']);

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
            'network_id' => $this->network_id,
            'player_id' => $this->player_id,
        ])
        ->andFilterWhere(['like','network_player.created_at',$this->created_at])
        ->andFilterWhere(['like','network_player.updated_at',$this->updated_at])
        ->andFilterWhere(['like','player.username',$this->username])
        ->andFilterWhere(['like','network.name',$this->network_name]);
        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'username' => [
                      'asc' =>  ['player.username' => SORT_ASC],
                      'desc' => ['player.username' => SORT_DESC],
                  ],
                  'network_name' => [
                    'asc' =>  ['network.name' => SORT_ASC],
                    'desc' => ['network.name' => SORT_DESC],
                ],
            ]
            ),
        ]);
        return $dataProvider;
    }
}
