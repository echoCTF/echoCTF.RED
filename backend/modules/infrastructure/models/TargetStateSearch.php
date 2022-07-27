<?php

namespace app\modules\infrastructure\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\infrastructure\models\TargetState;

/**
 * TargetStateSearch represents the model behind the search form of `app\modules\infrastructure\models\TargetState`.
 */
class TargetStateSearch extends TargetState
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'total_headshots', 'total_findings', 'total_treasures', 'player_rating', 'timer_avg', 'total_writeups', 'approved_writeups', 'finding_points', 'treasure_points', 'total_points', 'on_network', 'on_ondemand', 'ondemand_state'], 'integer'],
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
        $query = TargetState::find();

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
            'total_headshots' => $this->total_headshots,
            'total_findings' => $this->total_findings,
            'total_treasures' => $this->total_treasures,
            'player_rating' => $this->player_rating,
            'timer_avg' => $this->timer_avg,
            'total_writeups' => $this->total_writeups,
            'approved_writeups' => $this->approved_writeups,
            'finding_points' => $this->finding_points,
            'treasure_points' => $this->treasure_points,
            'total_points' => $this->total_points,
            'on_network' => $this->on_network,
            'on_ondemand' => $this->on_ondemand,
            'ondemand_state' => $this->ondemand_state,
        ]);

        return $dataProvider;
    }
}
