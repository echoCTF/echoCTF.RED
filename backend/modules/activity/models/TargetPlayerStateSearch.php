<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\TargetPlayerState;

/**
 * TargetPlayerStateSearch represents the model behind the search form of `app\modules\activity\models\TargetPlayerState`.
 */
class TargetPlayerStateSearch extends TargetPlayerState
{
    public $username;
    public $hostname;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'player_id', 'player_treasures', 'player_findings', 'player_points'], 'integer'],
            [['username','hostname','created_at','updated_at'], 'safe'],
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
        $query = TargetPlayerState::find()->joinWith(['player', 'target']);

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
            'target_player_state.id' => $this->id,
            'player_id' => $this->player_id,
            'player_treasures' => $this->player_treasures,
            'player_findings' => $this->player_findings,
            'player_points' => $this->player_points,
        ]);
        $query->andFilterWhere(['like', 'player.username', $this->username]);
        $query->andFilterWhere(['like', 'target.name', $this->hostname]);
        $query->andFilterWhere(['like', 'target_player_state.created_at', $this->created_at]);
        $query->andFilterWhere(['like', 'target_player_state.updated_at', $this->updated_at]);
        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'username' => [
                      'asc' => ['player.username' => SORT_ASC],
                      'desc' => ['player.username' => SORT_DESC],
                  ],
                  'hostname' => [
                      'asc' => ['target.name' => SORT_ASC],
                      'desc' => ['target.name' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
