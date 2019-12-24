<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\Arpdat;

/**
 * ArpdatSearch represents the model behind the search form of `app\modules\activity\models\Arpdat`.
 */
class ArpdatSearch extends Arpdat
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ip'], 'integer'],
            [['mac','ipoctet'], 'safe'],
            [['ipoctet'], 'ip'],
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
        $query = Arpdat::find();

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
            'ip' => $this->ip,
        ]);

        $query->andFilterWhere(['like', 'mac', $this->mac]);
        $query->andFilterWhere(['like', 'INET_NTOA(ip)', $this->ipoctet]);

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
