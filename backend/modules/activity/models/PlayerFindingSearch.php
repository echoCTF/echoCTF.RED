<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\PlayerFinding;

/**
 * PlayerFindingSearch represents the model behind the search form of `app\modules\activity\models\PlayerFinding`.
 */
class PlayerFindingSearch extends PlayerFinding
{
    public $player;
    public $finding;
    public $target_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'finding_id'], 'integer'],
            [['ts', 'player', 'finding', 'target_id', 'points'], 'safe'],
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
        $query = PlayerFinding::find()->joinWith(['player', 'finding']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'player_finding.player_id' => $this->player_id,
            'player_finding.finding_id' => $this->finding_id,
            'player_finding.points' => $this->points,
            'finding.target_id' => $this->target_id,
        ]);

        $query->andFilterWhere(['like', 'player_finding.ts', $this->ts]);
        $query->andFilterWhere(['like', 'player.username', $this->player]);
        $query->andFilterWhere(['like', 'finding.name', $this->finding]);

        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                    'target_id' => [
                        'asc' => ['treasure.target_id' => SORT_ASC],
                        'desc' => ['treasure.target_id' => SORT_DESC],
                    ],
                    'player' => [
                        'asc' => ['player.username' => SORT_ASC],
                        'desc' => ['player.username' => SORT_DESC],
                    ],
                    'finding' => [
                        'asc' => ['finding_id' => SORT_ASC],
                        'desc' => ['finding_id' => SORT_DESC],
                    ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
