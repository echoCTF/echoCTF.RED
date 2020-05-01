<?php

namespace app\modules\smartcity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\smartcity\models\InfrastructureTarget;

/**
 * InfrastructureTargetSearch represents the model behind the search form of `app\modules\gameplay\models\InfrastructureTarget`.
 */
class InfrastructureTargetSearch extends InfrastructureTarget
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['infrastructure_id', 'target_id'], 'integer'],
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
        $query=InfrastructureTarget::find();

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
            'infrastructure_id' => $this->infrastructure_id,
            'target_id' => $this->target_id,
        ]);

        return $dataProvider;
    }
}
