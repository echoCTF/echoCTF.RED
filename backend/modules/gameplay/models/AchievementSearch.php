<?php

namespace app\modules\gameplay\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\gameplay\models\Achievement;

/**
 * AchievementSearch represents the model behind the search form of `app\modules\gameplay\models\Achievement`.
 */
class AchievementSearch extends Achievement
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'appears'], 'integer'],
            [['name', 'pubname', 'description', 'pubdescription', 'player_type', 'effects', 'code'], 'safe'],
            [['points'], 'number'],
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
        $query=Achievement::find();

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
            'achievement.id' => $this->id,
            'achievement.points' => $this->points,
            'achievement.appears' => $this->appears,
        ]);

        $query->andFilterWhere(['like', 'achievement.name', $this->name])
            ->andFilterWhere(['like', 'achievement.pubname', $this->pubname])
            ->andFilterWhere(['like', 'achievement.description', $this->description])
            ->andFilterWhere(['like', 'achievement.pubdescription', $this->pubdescription])
            ->andFilterWhere(['like', 'achievement.player_type', $this->player_type])
            ->andFilterWhere(['like', 'achievement.effects', $this->effects])
            ->andFilterWhere(['like', 'achievement.code', $this->code]);

        return $dataProvider;
    }
}
