<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\ChallengeSolver;

/**
 * ChallengeSolverSearch represents the model behind the search form of `app\modules\activity\models\ChallengeSolver`.
 */
class ChallengeSolverSearch extends ChallengeSolver
{
  public $username,$challenge_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['challenge_id', 'player_id', 'timer', 'rating','first'], 'integer'],
            [['created_at','username','challenge_name'], 'safe'],
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
        $query = ChallengeSolver::find()->joinWith(['challenge','player']);

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
            'challenge_id' => $this->challenge_id,
            'player_id' => $this->player_id,
            'timer' => $this->timer,
            'rating' => $this->rating,
            'first' => $this->first,
        ]);
        $query->andFilterWhere(['like', 'challenge_solver.created_at', $this->created_at]);
        $query->andFilterWhere(['like', 'player.username', $this->username]);
        $query->andFilterWhere(['like', 'challenge.name', $this->challenge_name]);
        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'username' => [
                      'asc' => ['player.username' => SORT_ASC],
                      'desc' => ['player.username' => SORT_DESC],
                  ],
                  'challenge_name' => [
                      'asc' => ['challenge.name' => SORT_ASC],
                      'desc' => ['challenge.name' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
