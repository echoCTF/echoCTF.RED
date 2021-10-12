<?php

namespace app\modules\gameplay\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\gameplay\models\Treasure;

/**
 * TreasureSearch represents the model behind the search form of `app\modules\gameplay\models\Treasure`.
 */
class TreasureSearch extends Treasure
{
  public $ipoctet;
  public $discovered;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'appears', 'target_id','weight'], 'integer'],
            [['discovered', 'name', 'pubname', 'category', 'description', 'pubdescription', 'player_type', 'csum', 'effects', 'code', 'ipoctet'], 'safe'],
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
        $query=Treasure::find()->joinWith(['target']);
        $query->select('treasure.*,(SELECT COUNT(treasure_id) FROM player_treasure WHERE treasure.id=player_treasure.treasure_id) as discovered');

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
            'treasure.id' => $this->id,
            'treasure.points' => $this->points,
            'treasure.appears' => $this->appears,
            'treasure.weight' => $this->weight,
            'treasure.category' => $this->category,
            'treasure.target_id' => $this->target_id,
            '(SELECT COUNT(DISTINCT treasure_id)>0 FROM player_treasure WHERE treasure.id=player_treasure.treasure_id)' => $this->discovered,
        ]);

        $query->andFilterWhere(['like', 'treasure.name', $this->name])
            ->andFilterWhere(['like', 'treasure.pubname', $this->pubname])
            ->andFilterWhere(['like', 'treasure.description', $this->description])
            ->andFilterWhere(['like', 'treasure.pubdescription', $this->pubdescription])
            ->andFilterWhere(['like', 'treasure.player_type', $this->player_type])
            ->andFilterWhere(['like', 'treasure.csum', $this->csum])
            ->andFilterWhere(['like', 'treasure.effects', $this->effects])
            ->andFilterWhere(['like', 'treasure.code', $this->code]);

        $query->andFilterWhere(['OR',
                                ['like', 'INET_NTOA(target.ip)', $this->ipoctet],
                                ['like', 'target.name', $this->ipoctet],
                                ['=', 'target.id', $this->ipoctet]
                              ]);
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
