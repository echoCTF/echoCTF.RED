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
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ip', 'active', 'rootable','difficulty','required_xp','suggested_xp'], 'integer'],
            [['status'],'in', 'range' => ['online','offline','powerup','powerdown','maintenance']],
            [['scheduled_at'],'datetime'],
            [['name', 'fqdn', 'purpose', 'description', 'mac', 'net', 'server', 'image', 'dns', 'parameters', 'ipoctet','status','scheduled_at','required_xp','suggested_xp'], 'safe'],
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
        $query = Target::find();

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
            'target.id' => $this->id,
            'ip' => $this->ip,
            'active' => $this->active,
            'rootable' => $this->rootable,
            'difficulty' => $this->difficulty,
            'suggested_xp' => $this->suggested_xp,
            'required_xp' => $this->required_xp,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
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
        $dataProvider->setSort([
                'attributes' => array_merge(
                    $dataProvider->getSort()->attributes,
                    [
                      'ipoctet' => [
                          'asc' => ['ip' => SORT_ASC],
                          'desc' => ['ip' => SORT_DESC],
                      ],
                    ]
                ),
            ]);

        return $dataProvider;
    }
}
