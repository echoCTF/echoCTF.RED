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
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['target_id', 'player_id', 'state'], 'integer'],
            [['heartbeat', 'created_at', 'updated_at'], 'safe'],
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
        $query = TargetOndemand::find();

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
            'heartbeat' => $this->heartbeat,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        return $dataProvider;
    }
}
