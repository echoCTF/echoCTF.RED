<?php

namespace app\modules\settings\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\settings\models\Experience;

/**
 * ExperienceSearch represents the model behind the search form of `app\modules\settings\models\Experience`.
 */
class ExperienceSearch extends Experience
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'min_points', 'max_points'], 'integer'],
            [['name', 'description', 'icon', 'category'], 'safe'],
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
        $query=Experience::find();

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
            'experience.id' => $this->id,
            'experience.min_points' => $this->min_points,
            'experience.max_points' => $this->max_points,
        ]);

        $query->andFilterWhere(['like', 'experience.name', $this->name])
            ->andFilterWhere(['like', 'experience.description', $this->description])
            ->andFilterWhere(['like', 'experience.category', $this->category])
            ->andFilterWhere(['like', 'experience.icon', $this->icon]);

        return $dataProvider;
    }
}
