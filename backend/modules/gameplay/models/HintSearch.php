<?php

namespace app\modules\gameplay\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\gameplay\models\Hint;

/**
 * HintSearch represents the model behind the search form of `app\modules\gameplay\models\Hint`.
 */
class HintSearch extends Hint
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'badge_id', 'finding_id', 'treasure_id', 'question_id', 'points_user', 'points_team', 'timeafter', 'active'], 'integer'],
            [['title', 'player_type', 'message', 'category', 'ts'], 'safe'],
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
        $query=Hint::find()->with(['finding', 'treasure', 'question']);

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
            'hint.id' => $this->id,
            'hint.badge_id' => $this->badge_id,
            'hint.finding_id' => $this->finding_id,
            'hint.treasure_id' => $this->treasure_id,
            'hint.question_id' => $this->question_id,
            'hint.points_user' => $this->points_user,
            'hint.points_team' => $this->points_team,
            'hint.timeafter' => $this->timeafter,
            'hint.active' => $this->active,
            'hint.ts' => $this->ts,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'player_type', $this->player_type])
            ->andFilterWhere(['like', 'message', $this->message])
            ->andFilterWhere(['like', 'category', $this->category]);

        return $dataProvider;
    }
}
