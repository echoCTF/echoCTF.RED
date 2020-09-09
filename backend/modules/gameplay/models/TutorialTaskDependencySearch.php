<?php

namespace app\modules\gameplay\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\gameplay\models\TutorialTaskDependency;

/**
 * TutorialTaskDependencySearch represents the model behind the search form of `app\modules\gameplay\models\TutorialTaskDependency`.
 */
class TutorialTaskDependencySearch extends TutorialTaskDependency
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'tutorial_task_id', 'item_id'], 'integer'],
            [['item'], 'safe'],
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
        $query = TutorialTaskDependency::find();

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
            'tutorial_task_id' => $this->tutorial_task_id,
            'item_id' => $this->item_id,
        ]);

        $query->andFilterWhere(['like', 'item', $this->item]);

        return $dataProvider;
    }
}
