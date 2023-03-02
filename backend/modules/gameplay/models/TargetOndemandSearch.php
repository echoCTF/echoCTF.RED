<?php

namespace app\modules\gameplay\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\gameplay\models\TargetOndemand;

/**
 * TargetOndemandSearch represents the model behind the search form of `app\modules\gameplay\models\TargetOndemand`.
 */
class TargetOndemandSearch extends TargetOndemand
{
    public $name;
    public $username;
    public $ipoctet;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['target_id', 'player_id', 'state'], 'integer'],
            [['heartbeat', 'created_at', 'updated_at','name','ipoctet','username'], 'safe'],
            [['name','username','ipoctet'],'trim'],
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
        $query = TargetOndemand::find()->joinWith(['target','player']);

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
            'target_id' => $this->target_id,
            'player_id' => $this->player_id,
            'state' => $this->state,
        ]);
        $query->andFilterWhere(['like', 'target_ondemand.created_at', $this->created_at]);
        $query->andFilterWhere(['like', 'target_ondemand.heartbeat', $this->heartbeat]);
        $query->andFilterWhere(['like', 'target.name', $this->name]);
        $query->andFilterWhere(['like', 'player.username', $this->username]);
        $query->andFilterWhere(['like','INET_NTOA(target.ip)',$this->ipoctet]);
        $dataProvider->setSort([
            'defaultOrder' => ['state'=>SORT_DESC,'name'=>SORT_ASC,'created_at'=>SORT_DESC],
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                    'username' => [
                        'asc' => ['player.username' => SORT_ASC],
                        'desc' => ['player.username' => SORT_DESC],
                    ],
                    'name' => [
                        'asc' => ['target.name' => SORT_ASC],
                        'desc' => ['target.name' => SORT_DESC],
                    ],
                    'ipoctet' => [
                        'asc' => ['target.ip' => SORT_ASC],
                        'desc' => ['target.ip' => SORT_DESC],
                    ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
