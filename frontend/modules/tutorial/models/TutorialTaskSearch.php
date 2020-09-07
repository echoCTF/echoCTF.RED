<?php

namespace app\modules\tutorial\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\tutorial\models\TutorialTask;

/**
 * TutorialTaskSearch represents the model behind the search form of `app\modules\gameplay\models\TutorialTask`.
 */
class TutorialTaskSearch extends TutorialTask
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'tutorial_id', 'points', 'weight'], 'integer'],
            [['title', 'description', 'answer'], 'safe'],
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
        $query = TutorialTask::find();

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
            'tutorial_id' => $this->tutorial_id,
            'points' => $this->points,
            'weight' => $this->weight,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'answer', $this->answer]);

        return $dataProvider;
    }
}
