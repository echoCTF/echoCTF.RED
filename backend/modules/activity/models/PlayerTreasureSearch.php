<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\PlayerTreasure;

/**
 * PlayerTreasureSearch represents the model behind the search form of `app\modules\activity\models\PlayerTreasure`.
 */
class PlayerTreasureSearch extends PlayerTreasure
{
    public $player;
    public $treasure;
    public $target_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'treasure_id'], 'integer'],
            [['ts', 'player', 'treasure', 'target_id','points'], 'safe'],
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
        $query=PlayerTreasure::find()->joinWith(['player', 'treasure']);

        // add conditions that should always apply here

        $dataProvider=new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if(!$this->validate())
        {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'player_treasure.player_id' => $this->player_id,
            'player_treasure.treasure_id' => $this->treasure_id,
            'player_treasure.points' => $this->points,
            'treasure.target_id' => $this->target_id,
        ]);

        $query->andFilterWhere(['like', 'player_treasure.ts', $this->ts]);
        $query->andFilterWhere(['like', 'player.username', $this->player]);
        $query->andFilterWhere(['like', 'treasure.name', $this->treasure]);

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
                  'treasure' => [
                      'asc' => ['treasure_id' => SORT_ASC],
                      'desc' => ['treasure_id' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
