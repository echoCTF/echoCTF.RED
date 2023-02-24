<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\PlayerBadge;

/**
 * PlayerBadgeSearch represents the model behind the search form of `app\modules\activity\models\PlayerBadge`.
 */
class PlayerBadgeSearch extends PlayerBadge
{
    public $player;
    public $badge;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'badge_id'], 'integer'],
            [['ts', 'player', 'badge'], 'safe'],
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
        $query=PlayerBadge::find()->joinWith(['player', 'badge']);

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
            'player_badge.player_id' => $this->player_id,
            'player_badge.badge_id' => $this->badge_id,
        ]);

        $query->andFilterWhere(['like', 'player_badge.ts', $this->ts]);
        $query->andFilterWhere(['like', 'player.username', $this->player]);
        $query->andFilterWhere(['like', 'badge.name', $this->badge]);

        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'player' => [
                      'asc' => ['player.username' => SORT_ASC],
                      'desc' => ['player.username' => SORT_DESC],
                  ],
                  'badge' => [
                      'asc' => ['badge_id' => SORT_ASC],
                      'desc' => ['badge_id' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
