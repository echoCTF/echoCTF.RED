<?php

namespace app\modules\sales\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sales\models\Price;

/**
 * PriceSearch represents the model behind the search form of `app\modules\sales\models\Price`.
 */
class PriceSearch extends Price
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
          [['id','recurring_interval','metadata','currency','nickname','product_id','ptype'], 'string'],
          [['active'], 'boolean'],
          [['unit_amount','interval_count'],'safe']
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
        $query = Price::find();

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
            'active'=>$this->active,
            'unit_amount'=>$this->active
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'product_id', $this->product_id]);

        return $dataProvider;
    }
}
