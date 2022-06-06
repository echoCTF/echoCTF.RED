<?php

namespace app\modules\gameplay\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\gameplay\models\NetworkTarget;

/**
 * NetworkTargetSearch represents the model behind the search form of `app\modules\gameplay\models\NetworkTarget`.
 */
class NetworkTargetSearch extends NetworkTarget
{
    public $network_name;
    public $target_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['network_id', 'target_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['network_name','target_name'],'safe'],
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
        $query=NetworkTarget::find()->joinWith(['network','target']);

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
            'network_id' => $this->network_id,
            'target_id' => $this->target_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        $query->andFilterWhere(['LIKE','network.name',$this->network_name]);
        $query->andFilterWhere(['LIKE','target.name',$this->target_name]);
        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'target_name' => [
                      'asc' => ['target.name' => SORT_ASC],
                      'desc' => ['target.name' => SORT_DESC],
                  ],
                  'network_name' => [
                    'asc' => ['network.name' => SORT_ASC],
                    'desc' => ['network.name' => SORT_DESC],
                ],
              ]
            ),
        ]);

        return $dataProvider;
    }
}
