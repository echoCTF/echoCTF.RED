<?php

namespace app\modules\gameplay\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\gameplay\models\Finding;

/**
 * FindingSearch represents the model behind the search form of `app\modules\gameplay\models\Finding`.
 */
class FindingSearch extends Finding
{
  public $ipoctet;
  public $discovered;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'stock', 'target_id', 'port'], 'integer'],
            [['discovered', 'name', 'pubname', 'description', 'pubdescription', 'protocol', 'ipoctet'], 'safe'],
            [['points'], 'number'],
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
        $query=Finding::find()->joinWith(['target']);
        $query->select('finding.*,(SELECT COUNT(finding_id) FROM player_finding WHERE finding.id=player_finding.finding_id) as discovered');

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
            'finding.id' => $this->id,
            'finding.points' => $this->points,
            'finding.stock' => $this->stock,
            'finding.target_id' => $this->target_id,
            'finding.port' => $this->port,
            '(SELECT COUNT(DISTINCT finding_id)>0 FROM player_finding WHERE finding.id=player_finding.finding_id)' => $this->discovered,
        ]);


        $query->andFilterWhere(['like', 'finding.name', $this->name])
            ->andFilterWhere(['like', 'pubname', $this->pubname])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'pubdescription', $this->pubdescription])
            ->andFilterWhere(['like', 'protocol', $this->protocol]);
        $query->andFilterWhere(['like', 'INET_NTOA(target.ip)', $this->ipoctet]);
        $query->orFilterWhere(['like', 'target.name', $this->ipoctet]);
        $query->orFilterWhere(['like', 'target.id', $this->ipoctet]);

        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'ipoctet' => [
                      'asc' => ['ip' => SORT_ASC],
                      'desc' => ['ip' => SORT_DESC],
                  ],
                  'discovered' => [
                      'asc' => ['discovered' => SORT_ASC],
                      'desc' => ['discovered' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
