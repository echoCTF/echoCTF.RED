<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\Writeup;

/**
 * WriteupSearch represents the model behind the search form of `app\modules\activity\models\Writeup`.
 */
class WriteupSearch extends Writeup
{
  public $username;
  public $name;
  public $lang;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'target_id', 'approved'], 'integer'],
            [['content', 'status', 'comment', 'created_at', 'updated_at', 'username', 'name', 'lang'], 'safe'],
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
        $query = Writeup::find()->joinWith(['player', 'target','language']);

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
            'target_id' => $this->target_id,
            'approved' => $this->approved,
        ]);

        $query->andFilterWhere(['like', 'writeup.created_at', $this->created_at])
              ->andFilterWhere(['like', 'writeup.updated_at', $this->updated_at])
              ->andFilterWhere(['like', 'content', $this->content])
              ->andFilterWhere(['like', 'writeup.status', $this->status])
              ->andFilterWhere(['like', 'comment', $this->comment]);
        $query->andFilterWhere(['like', 'player.username', $this->username]);
        $query->andFilterWhere(['language_id'=> $this->lang]);
        $query->andFilterWhere(['like', 'target.name', $this->name]);

        $dataProvider->setSort([
            'defaultOrder'=>['id'=>SORT_DESC],
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'username' => [
                      'asc' => ['player.username' => SORT_ASC],
                      'desc' => ['player.username' => SORT_DESC],
                  ],
                  'lang' => [
                    'asc' => ['language.l' => SORT_ASC],
                    'desc' => ['language.l' => SORT_DESC],
                ],
                'name' => [
                      'asc' => ['target.name' => SORT_ASC],
                      'desc' => ['target.name' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
