<?php

namespace app\modules\sales\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sales\models\PlayerSubscription;

/**
 * PlayerSubscriptionSearch represents the model behind the search form of `app\modules\sales\models\PlayerSubscription`.
 */
class PlayerSubscriptionSearch extends PlayerSubscription
{
    public $username;
    public $product_name;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'active'], 'integer'],
            [['subscription_id', 'session_id', 'price_id', 'created_at', 'updated_at','starting','ending','username','product_name'], 'safe'],
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
        $query = PlayerSubscription::find()->joinWith(['player','product']);

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
            'player_id' => $this->player_id,
            'player_subscription.active' => $this->active,
        ]);

        $query->andFilterWhere(['like', 'subscription_id', $this->subscription_id])
            ->andFilterWhere(['like', 'session_id', $this->session_id])
            ->andFilterWhere(['like', 'starting', $this->starting])
            ->andFilterWhere(['like', 'ending', $this->ending])
            ->andFilterWhere(['like', 'player.username', $this->username])
            ->andFilterWhere(['like', 'price_id', $this->price_id])
            ->andFilterWhere(['like', 'product.name', $this->product_name]);

        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                    'username' => [
                        'asc' => ['player.username' => SORT_ASC],
                        'desc' => ['player.username' => SORT_DESC],
                    ],
                    'product_name' => [
                        'asc' => ['product.name' => SORT_ASC],
                        'desc' => ['product.name' => SORT_DESC],
                    ],
                ]
            ),
        ]);
        return $dataProvider;
    }
}
