<?php

namespace app\modules\infrastructure\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\infrastructure\models\TargetMetadata;

/**
 * TargetMetadataSearch represents the model behind the search form of `app\modules\infrastructure\models\TargetMetadata`.
 */
class TargetMetadataSearch extends TargetMetadata
{
    public $target_name;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['target_id'], 'integer'],
            [['scenario', 'instructions', 'solution', 'pre_credits', 'post_credits', 'pre_exploitation', 'post_exploitation', 'created_at', 'updated_at','target_name'], 'safe'],
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
        $query = TargetMetadata::find()->joinWith(['target']);

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
            'target_id' => $this->target_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'scenario', $this->scenario])
            ->andFilterWhere(['like', 'target.name', $this->target_name])
            ->andFilterWhere(['like', 'instructions', $this->instructions])
            ->andFilterWhere(['like', 'solution', $this->solution])
            ->andFilterWhere(['like', 'pre_credits', $this->pre_credits])
            ->andFilterWhere(['like', 'post_credits', $this->post_credits])
            ->andFilterWhere(['like', 'pre_exploitation', $this->pre_exploitation])
            ->andFilterWhere(['like', 'post_exploitation', $this->post_exploitation]);

        $dataProvider->setSort([
            'defaultOrder' => ['created_at'=>SORT_DESC, 'target_name'=>SORT_ASC],
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'target_name' => [
                      'asc' => ['target.name' => SORT_ASC],
                      'desc' => ['target.name' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
