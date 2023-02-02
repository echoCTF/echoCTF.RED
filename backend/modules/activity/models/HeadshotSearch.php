<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\Headshot;

/**
 * HeadshotSearch represents the model behind the search form of `app\modules\activity\models\Headshot`.
 */
class HeadshotSearch extends Headshot
{
  public $username;
  public $fqdn;
  public $ipoctet;
  public $name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'target_id','timer','first','rating'], 'integer'],
            [['created_at', 'username', 'fqdn', 'ipoctet','name'], 'safe'],
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
        $query=Headshot::find()->joinWith(['player', 'target']);

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
            'player_id' => $this->player_id,
            'target_id' => $this->target_id,
            'first' => $this->first,
            'rating' => $this->rating,
        ]);
        $query->andFilterWhere(['like', 'headshot.created_at', $this->created_at]);
        $query->andFilterWhere(['like', 'player.username', $this->username]);
        $query->andFilterWhere(['like', 'target.name', $this->name]);
        $query->andFilterWhere(['like', 'INET_NTOA(target.ip)', $this->ipoctet]);
        $dataProvider->setSort([
            'defaultOrder' => ['created_at'=>SORT_DESC, 'player_id'=>SORT_ASC,'target_id'=>SORT_ASC],
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'username' => [
                      'asc' => ['player.username' => SORT_ASC],
                      'desc' => ['player.username' => SORT_DESC],
                  ],
                  'name' => [
                      'asc' => ['target.name' => SORT_ASC],
                      'desc' => ['target.name' => SORT_DESC],
                  ],
                  'ipoctet' => [
                      'asc' => ['target.ip' => SORT_ASC],
                      'desc' => ['target.ip' => SORT_DESC],
                  ],
                  'created_at' => [
                    'asc'=>['headshot.created_at' => SORT_ASC],
                    'desc'=>['headshot.created_at' => SORT_DESC]
                  ]
                ]
            ),
        ]);


        return $dataProvider;
    }
}
