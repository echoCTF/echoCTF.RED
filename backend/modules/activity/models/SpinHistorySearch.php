<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\SpinHistory;

/**
 * SpinHistorySearch represents the model behind the search form of `app\modules\activity\models\SpinHistory`.
 */
class SpinHistorySearch extends SpinHistory
{
  public $target;
  public $player;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'target_id', 'player_id'], 'integer'],
            [['created_at', 'updated_at','target','player'], 'safe'],
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
        $query = SpinHistory::find()->joinWith(['target','player']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate())
        {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'spin_history.id' => $this->id,
            'spin_history.target_id' => $this->target_id,
            'spin_history.player_id' => $this->player_id,
            'spin_history.created_at' => $this->created_at,
            'spin_history.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'player.username', $this->player]);
        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'player' => [
                      'asc' => [ 'spin_history.player_id' => SORT_ASC],
                      'desc' => ['spin_history.player_id' => SORT_DESC],
                  ],
                ]
            ),
        ]);


        $query->andFilterWhere(['like', 'target.fqdn', $this->target]);
        $dataProvider->setSort([
            'defaultOrder' => ['id'=>SORT_DESC],
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'target' => [
                      'asc' => [ 'target_id' => SORT_ASC],
                      'desc' => ['target_id' => SORT_DESC],
                  ],
                ]
            ),
        ]);


        return $dataProvider;
    }
}
