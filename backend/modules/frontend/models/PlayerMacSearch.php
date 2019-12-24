<?php

namespace app\modules\frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\frontend\models\PlayerMac;

/**
 * PlayerMacSearch represents the model behind the search form of `app\modules\frontend\models\PlayerMac`.
 */
class PlayerMacSearch extends PlayerMac
{
    public $player;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'player_id'], 'integer'],
            [['mac', 'player'], 'safe'],
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
        $query = PlayerMac::find()->joinWith(['player']);

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
            'player_mac.id' => $this->id,
            'player_id' => $this->player_id,
        ]);

        $query->andFilterWhere(['like', 'mac', $this->mac]);
        $query->andFilterWhere(['like', 'player.id', $this->player]);
        $query->orFilterWhere(['like', 'player.username', $this->player]);

        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'player' => [
                      'asc' => [ 'player_id' => SORT_ASC],
                      'desc' => ['player_id' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
