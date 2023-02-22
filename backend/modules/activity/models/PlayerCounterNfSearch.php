<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\PlayerCounterNf;

/**
 * PlayerCounterNfSearch represents the model behind the search form of `app\modules\activity\models\PlayerCounterNf`.
 */
class PlayerCounterNfSearch extends PlayerCounterNf
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'counter'], 'integer'],
            [['metric'], 'safe'],
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
        $query = PlayerCounterNf::find();

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
            'player_id' => $this->player_id,
            'counter' => $this->counter,
            'metric'=>$this->metric,
        ]);

        $query->andFilterWhere(['like', 'metric', $this->metric]);

        return $dataProvider;
    }
}
