<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\PlayerQuestion;

/**
 * PlayerQuestionSearch represents the model behind the search form of `app\modules\activity\models\PlayerQuestion`.
 */
class PlayerQuestionSearch extends PlayerQuestion
{
    public $player;
    public $question;
    public $challenge_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','challenge_id'], 'integer'],
            [['points'], 'number'],
            [['ts', 'player', 'question', 'question_id', 'player_id','challenge_id'], 'safe'],
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
        $query=PlayerQuestion::find()->joinWith(['question', 'player','question.challenge']);

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
            'player_question.id' => $this->id,
            'player_question.question_id' => $this->question_id,
            'player_question.player_id' => $this->player_id,
            'player_question.points' => $this->points,
        ]);
        $query->andFilterWhere(['like', 'player.username', $this->player]);
        $query->andFilterWhere(['like', 'player_question.ts', $this->ts]);
        $query->andFilterWhere(['like', 'question.name', $this->question]);
        $query->andFilterWhere(['question.challenge_id'=> $this->challenge_id]);
        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'question' => [
                      'asc' => ['question.name' => SORT_ASC],
                      'desc' => ['question.name' => SORT_DESC],
                  ],
                  'challenge_id' => [
                      'asc' => ['question.challenge_id' => SORT_ASC],
                      'desc' => ['question.challenge_id' => SORT_DESC],
                  ],
                  'player' => [
                      'asc' => ['player.username' => SORT_ASC],
                      'desc' => ['player.username' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
