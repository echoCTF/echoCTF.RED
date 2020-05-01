<?php

namespace app\modules\gameplay\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\gameplay\models\Question;

/**
 * QuestionSearch represents the model behind the search form of `app\modules\gameplay\models\Question`.
 */
class QuestionSearch extends Question
{
    public $challengename;
    public $answered;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'challenge_id', 'weight'], 'integer'],
            [['answered', 'name', 'description', 'player_type', 'code', 'challengename'], 'safe'],
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
        $query=Question::find()->joinWith('challenge');

        $query->select('question.*,(SELECT COUNT(question_id) FROM player_question WHERE question.id=player_question.question_id) as answered');
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
            'question.id' => $this->id,
            'question.challenge_id' => $this->challenge_id,
            'question.points' => $this->points,
            'question.weight' => $this->weight,
            '(SELECT COUNT(DISTINCT question_id)>0 FROM player_question WHERE question.id=player_question.question_id)' => $this->answered,
        ]);

        $query->andFilterWhere(['like', 'question.name', $this->name])
            ->andFilterWhere(['like', 'question.description', $this->description])
            ->andFilterWhere(['like', 'question.player_type', $this->player_type])
            ->andFilterWhere(['like', 'question.code', $this->code]);
        $query->andFilterWhere(['like', 'challenge.name', $this->challengename]);

        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'challengename' => [
                      'asc' => ['challengename' => SORT_ASC],
                      'desc' => ['challengename' => SORT_DESC],
                  ],
                  'answered' => [
                      'asc' => ['answered' => SORT_ASC],
                      'desc' => ['answered' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
