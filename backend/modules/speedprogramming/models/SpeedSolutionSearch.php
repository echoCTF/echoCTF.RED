<?php

namespace app\modules\speedprogramming\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\speedprogramming\models\SpeedSolution;

/**
 * SpeedSolutionSearch represents the model behind the search form of `app\models\SpeedSolution`.
 */
class SpeedSolutionSearch extends SpeedSolution
{
  public $username;
  public $problem;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'player_id', 'points'], 'integer'],
            [['username','language', 'sourcecode', 'status', 'created_at', 'updated_at','problem_id','problem'], 'safe'],
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
        $query = SpeedSolution::find()->joinWith(['player','problem']);

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
            'player_id' => $this->player_id,
            'problem_id' => $this->problem_id,
            'points' => $this->points,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'language', $this->language])
        ->andFilterWhere(['like', 'sourcecode', $this->sourcecode])
        ->andFilterWhere(['like', 'player.username', $this->username])
        ->andFilterWhere(['like', 'speed_problem.name', $this->problem])
        ->andFilterWhere(['like', 'status', $this->status]);

        $dataProvider->setSort([
          'attributes' => array_merge(
              $dataProvider->getSort()->attributes,
              [
                  'username' => [
                      'asc' => ['player.username' => SORT_ASC],
                      'desc' => ['player.username' => SORT_DESC],
                  ],
                  'problem' => [
                    'asc' => ['problem.name' => SORT_ASC],
                    'desc' => ['problem.name' => SORT_DESC],
                ],
            ]
          ),
      ]);

        return $dataProvider;
    }
}
