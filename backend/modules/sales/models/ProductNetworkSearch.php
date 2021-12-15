<?php

namespace app\modules\sales\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sales\models\ProductNetwork;

/**
 * ProductNetworkSearch represents the model behind the search form of `app\modules\sales\models\ProductNetwork`.
 */
class ProductNetworkSearch extends ProductNetwork
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id'], 'safe'],
            [['network_id'], 'integer'],
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
        $query = ProductNetwork::find();

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
            'network_id' => $this->network_id,
        ]);

        $query->andFilterWhere(['like', 'product_id', $this->product_id]);

        return $dataProvider;
    }
}
