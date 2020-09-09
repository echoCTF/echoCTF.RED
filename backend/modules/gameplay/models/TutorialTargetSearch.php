<?php

namespace app\modules\gameplay\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\gameplay\models\TutorialTarget;

/**
 * TutorialTargetSearch represents the model behind the search form of `app\modules\gameplay\models\TutorialTarget`.
 */
class TutorialTargetSearch extends TutorialTarget
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tutorial_id', 'target_id', 'weight'], 'integer'],
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
        $query = TutorialTarget::find();

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
            'tutorial_id' => $this->tutorial_id,
            'target_id' => $this->target_id,
            'weight' => $this->weight,
        ]);

        return $dataProvider;
    }
}
