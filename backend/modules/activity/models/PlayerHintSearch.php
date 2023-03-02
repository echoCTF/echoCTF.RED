<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\PlayerHint;

/**
 * PlayerHintSearch represents the model behind the search form of `app\modules\activity\models\PlayerHint`.
 */
class PlayerHintSearch extends PlayerHint
{
    public $player;
    public $hint;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'hint_id', 'status'], 'integer'],
            [['ts', 'player', 'hint'], 'safe'],
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
        $query=PlayerHint::find()->joinWith(['player', 'hint']);

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
            'player_hint.player_id' => $this->player_id,
            'player_hint.hint_id' => $this->hint_id,
            'player_hint.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'player_hint.ts', $this->ts]);
        $query->andFilterWhere(['like', 'player.username', $this->player]);
        $query->andFilterWhere(['like', 'hint.title', $this->hint]);

        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'player' => [
                      'asc' => ['player.username' => SORT_ASC],
                      'desc' => ['player.username' => SORT_DESC],
                  ],
                  'hint' => [
                      'asc' => ['hint_id' => SORT_ASC],
                      'desc' => ['hint_id' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
