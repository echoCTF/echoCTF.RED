<?php

namespace app\modules\frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\frontend\models\PlayerRelation;

/**
 * PlayerRelationSearch represents the model behind the search form of `app\modules\frontend\models\PlayerRelation`.
 */
class PlayerRelationSearch extends PlayerRelation
{
  public $player;
  public $referred;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'referred_id'], 'integer'],
            [['player','referred'],'safe'],
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
        $query = PlayerRelation::find()->joinWith(['player'])->leftJoin('player referred','referred.id=player_relation.referred_id');

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
            'player_id' => $this->player_id,
            'referred_id' => $this->referred_id,
        ]);
        $query->andFilterWhere(['like', 'player.username', $this->player]);
        $query->orFilterWhere(['like', 'referred.username', $this->referred]);
        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'player' => [
                      'asc' => ['player.username' => SORT_ASC],
                      'desc' => ['player.username' => SORT_DESC],
                  ],
                  'referred' => [
                      'asc' => ['referred.username' => SORT_ASC],
                      'desc' => ['referred.username' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
