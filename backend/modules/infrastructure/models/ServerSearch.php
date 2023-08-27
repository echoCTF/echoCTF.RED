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
            ->andFilterWhere(['like', 'provider_id', $this->provider_id])
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
