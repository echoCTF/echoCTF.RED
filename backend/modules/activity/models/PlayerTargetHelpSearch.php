<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\PlayerTargetHelp;

/**
 * PlayerTargetHelpSearch represents the model behind the search form of `app\modules\activity\models\PlayerTargetHelp`.
 */
class PlayerTargetHelpSearch extends PlayerTargetHelp
{
  public $username,$target_name;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'target_id'], 'integer'],
            [['created_at','username','target_name'], 'safe'],
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
        $query = PlayerTargetHelp::find()->joinWith(['target','player']);

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
            'player_target_help.player_id' => $this->player_id,
            'player_target_help.target_id' => $this->target_id,
        ]);
        $query->andFilterWhere(['like', 'player.username', $this->username]);
        $query->andFilterWhere(['like', 'player_target_help.created_at', $this->created_at]);
        $query->andFilterWhere(['like', 'target.name', $this->target_name]);
        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'username' => [
                      'asc' => ['player.username' => SORT_ASC],
                      'desc' => ['player.username' => SORT_DESC],
                  ],
                  'target_name' => [
                      'asc' => ['target.name' => SORT_ASC],
                      'desc' => ['target.name' => SORT_DESC],
                  ],
                  'created_at' => [
                    'asc' =>  ['player_target_help.created_at' => SORT_ASC],
                    'desc' => ['player_target_help.created_at' => SORT_DESC],
                ],
              ]
            ),
        ]);

        return $dataProvider;
    }
}
