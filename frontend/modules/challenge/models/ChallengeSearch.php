<?php

namespace app\modules\challenge\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ChallengeSearch represents the model behind the search form of `app\models\Challenge`.
 */
class ChallengeSearch extends Challenge
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'category', 'difficulty', 'description', 'player_type', 'filename', 'ts'], 'safe'],
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
        $query=Challenge::find();

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
            'id' => $this->id,
            'ts' => $this->ts,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'category', $this->category])
            ->andFilterWhere(['like', 'difficulty', $this->difficulty])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'player_type', $this->player_type])
            ->andFilterWhere(['like', 'filename', $this->filename]);

        return $dataProvider;
    }
}
