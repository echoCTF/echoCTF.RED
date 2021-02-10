<?php

namespace app\modules\gameplay\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\gameplay\models\Challenge;

/**
 * ChallengeSearch represents the model behind the search form of `app\modules\gameplay\models\Challenge`.
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
            [['active','timer','public'], 'boolean'],
            [['name', 'category', 'difficulty', 'description', 'player_type', 'filename'], 'safe'],
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
            'challenge.id' => $this->id,
            'challenge.active' => $this->active,
            'challenge.public' => $this->public,
        ]);

        $query->andFilterWhere(['like', 'challenge.name', $this->name])
            ->andFilterWhere(['like', 'challenge.category', $this->category])
            ->andFilterWhere(['like', 'challenge.difficulty', $this->difficulty])
            ->andFilterWhere(['like', 'challenge.description', $this->description])
            ->andFilterWhere(['like', 'challenge.player_type', $this->player_type])
            ->andFilterWhere(['like', 'challenge.filename', $this->filename]);

        return $dataProvider;
    }
}
