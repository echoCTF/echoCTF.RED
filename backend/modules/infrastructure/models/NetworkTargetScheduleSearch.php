<?php

namespace app\modules\infrastructure\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\infrastructure\models\NetworkTargetSchedule;

/**
 * NetworkTargetScheduleSearch represents the model behind the search form of `app\modules\infrastructure\models\NetworkTargetSchedule`.
 */
class NetworkTargetScheduleSearch extends NetworkTargetSchedule
{
    public $network_name;
    public $target_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'target_id', 'network_id'], 'integer'],
            [['migration_date', 'created_at', 'updated_at','network_name','target_name'], 'safe'],
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
        $query = NetworkTargetSchedule::find()->joinWith(['target','network']);

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
            'network_target_schedule.id' => $this->id,
            'network_target_schedule.target_id' => $this->target_id,
        ]);
        $query->andFilterWhere(['like','network_target_schedule.created_at',$this->created_at])
              ->andFilterWhere(['like','network_target_schedule.updated_at',$this->updated_at]);
        $query->andFilterWhere([
            'OR',
            ['like','target.name',$this->target_name],
            ['like','network.name',$this->network_name],
            ['like','migration_date', $this->migration_date],

        ]);
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
