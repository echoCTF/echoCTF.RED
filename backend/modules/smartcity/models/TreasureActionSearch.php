<?php

namespace app\modules\smartcity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\smartcity\models\TreasureAction;

/**
 * TreasureActionSearch represents the model behind the search form of `app\modules\gameplay\models\TreasureAction`.
 */
class TreasureActionSearch extends TreasureAction
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'treasure_id', 'ip', 'port', 'weight'], 'integer'],
            [['command', 'ipoctet'], 'safe'],
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
        $query=TreasureAction::find();

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
            'id' => $this->id,
            'treasure_id' => $this->treasure_id,
            'ip' => $this->ip,
            'port' => $this->port,
            'weight' => $this->weight,
        ]);

        $query->andFilterWhere(['like', 'command', $this->command])
              ->andFilterWhere(['like', 'INET_NTOA(ip)', $this->ipoctet]);
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
