<?php

namespace app\modules\frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\frontend\models\PlayerSpin;

/**
 * PlayerSpinSearch represents the model behind the search form of `app\modules\frontend\models\PlayerSpin`.
 */
class PlayerSpinSearch extends PlayerSpin
{
  public $player;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'counter', 'total','perday'], 'integer'],
            [['updated_at', 'player','ts'], 'safe'],
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
        $query=PlayerSpin::find()->joinWith(['player']);

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
            'player_spin.player_id' => $this->player_id,
            'player_spin.counter' => $this->counter,
            'player_spin.perday' => $this->perday,
            'player_spin.total' => $this->total,
        ]);
        $query->andFilterWhere(['like', 'player_spin.updated_at', $this->updated_at]);
        $query->andFilterWhere(['like', 'player_spin.ts', $this->ts]);
        $query->andFilterWhere(['like', 'player.id', $this->player]);
        $query->orFilterWhere(['like', 'player.username', $this->player]);
        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
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
