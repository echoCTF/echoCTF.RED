<?php

namespace app\modules\gameplay\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\gameplay\models\Target;

/**
 * TargetSearch represents the model behind the search form of `app\modules\gameplay\models\Target`.
 */
class TargetSearch extends Target
{
  public $headshot;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ip', 'timer','rootable', 'difficulty', 'required_xp', 'suggested_xp','headshot','weight'], 'integer'],
            ['active','boolean'],
            [['headshot'],'default','value'=>null ],
            [['status'], 'in', 'range' => ['online', 'offline', 'powerup', 'powerdown', 'maintenance']],
            [['scheduled_at'], 'datetime'],
            [['timer','name', 'fqdn', 'purpose', 'description', 'mac', 'net', 'server', 'image', 'dns', 'parameters', 'ipoctet', 'status', 'scheduled_at', 'required_xp', 'suggested_xp','headshot','category'], 'safe'],
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
        $query=Target::find()->joinWith(['headshots']);

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
            'target.id' => $this->id,
            'ip' => $this->ip,
            'active' => $this->active,
            'rootable' => $this->rootable,
            'difficulty' => $this->difficulty,
            'suggested_xp' => $this->suggested_xp,
            'required_xp' => $this->required_xp,
            'weight' => $this->weight,
            'timer' => $this->timer,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'category', $this->status])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'scheduled_at', $this->scheduled_at])
            ->andFilterWhere(['like', 'INET_NTOA(ip)', $this->ipoctet])
            ->andFilterWhere(['like', 'fqdn', $this->fqdn])
            ->andFilterWhere(['like', 'purpose', $this->purpose])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'mac', $this->mac])
            ->andFilterWhere(['like', 'net', $this->net])
            ->andFilterWhere(['like', 'server', $this->server])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'dns', $this->dns])
            ->andFilterWhere(['like', 'parameters', $this->parameters]);
            ;

        if($this->headshot !== null ) $query->having(["=",'count(headshot.player_id)',$this->headshot]);
        $query->groupBy(['target.id']);
        $dataProvider->setSort([
                'attributes' => array_merge(
                    $dataProvider->getSort()->attributes,
                    [
                      'target.id' => [
                          'asc' => ['target.id' => SORT_ASC],
                          'desc' => ['target.id' => SORT_DESC],
                      ],
                      'ipoctet' => [
                          'asc' => ['ip' => SORT_ASC],
                          'desc' => ['ip' => SORT_DESC],
                      ],
                      'headshot' => [
                          'asc' => ['COUNT(headshot.player_id)' => SORT_ASC],
                          'desc' => ['COUNT(headshot.player_id)' => SORT_DESC],
                      ],
                    ]
                ),
            ]);

        return $dataProvider;
    }
}
