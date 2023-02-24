<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\Report;

/**
 * ReportSearch represents the model behind the search form of `app\modules\activity\models\Report`.
 */
class ReportSearch extends Report
{
    public $player;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'player_id', 'points'], 'integer'],
            [['title', 'body', 'status', 'modcomment', 'pubtitle', 'pubbody', 'player'], 'safe'],
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
        $query=Report::find()->joinWith(['player']);

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
            'report.id' => $this->id,
            'report.player_id' => $this->player_id,
            'report.points' => $this->points,
        ]);

        $query->andFilterWhere(['like', 'report.title', $this->title])
            ->andFilterWhere(['like', 'report.body', $this->body])
            ->andFilterWhere(['like', 'report.status', $this->status])
            ->andFilterWhere(['like', 'report.modcomment', $this->modcomment])
            ->andFilterWhere(['like', 'report.pubtitle', $this->pubtitle])
            ->andFilterWhere(['like', 'report.pubbody', $this->pubbody])
            ->andFilterWhere(['like', 'player.username', $this->player]);

        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'player' => [
                      'asc' =>  ['player.username' => SORT_ASC],
                      'desc' => ['player.username' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
