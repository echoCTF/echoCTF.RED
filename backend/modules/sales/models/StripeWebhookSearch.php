<?php

namespace app\modules\sales\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sales\models\StripeWebhook;

/**
 * StripeWebhookSearch represents the model behind the search form of `app\modules\sales\models\StripeWebhook`.
 */
class StripeWebhookSearch extends StripeWebhook
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['type', 'object', 'object_id', 'ts'], 'safe'],
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
        $query = StripeWebhook::find();

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
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
        ->andFilterWhere(['like', 'ts', $this->ts])
        ->andFilterWhere(['like', 'object_id', $this->object_id])
        ->andFilterWhere(['like', 'object', $this->object]);
        $dataProvider->setSort([
             'defaultOrder' => ['ts'=>SORT_DESC,'id'=>SORT_DESC]]);
        return $dataProvider;
    }
}
