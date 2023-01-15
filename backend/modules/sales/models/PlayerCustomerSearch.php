<?php

namespace app\modules\sales\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\frontend\models\Player;
use yii\helpers\Html;

/**
 * PlayerSearch represents the model behind the search form of `app\modules\frontend\models\Player`.
 */
class PlayerCustomerSearch extends Player
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['created','username','fullname','email', 'stripe_customer_id'], 'safe'],
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
        $query=Player::find()->where('stripe_customer_id is not null');
        $dataProvider=new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if(!$this->validate())
        {
            $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'fullname', $this->fullname])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like','stripe_customer_id',$this->stripe_customer_id])
            ->andFilterWhere(['like', 'created', $this->created]);

        return $dataProvider;
    }

    /**
     * Fetch stripe customers and update their stripe_customer_id based on emails
     */
    public static function FetchStripe()
    {
      $stripe = new \Stripe\StripeClient(\Yii::$app->sys->stripe_apiKey);
      $stripe_customers=$stripe->customers->all([]);
      foreach($stripe_customers->data as $customer)
      {
        if(isset($customer->metadata->player_id))
        {
          $filter=$customer->metadata->player_id;
        }
        else
        {
          $filter=['email'=>$customer->email];
        }
        $player=Player::findOne($filter);

        if($player!==null)
        {
          $player->updateAttributes(['stripe_customer_id'=>$customer->id]);
          if(\Yii::$app instanceof \yii\console\Application)
            printf("Imported customer_id: %s for user %s with email %s\n",$player->stripe_customer_id,$player->username,$player->email);
          else
            \Yii::$app->session->addFlash('success', sprintf('Imported customer_id: <b>%s</b> for user <b>%s</b> with email <b>%s</b>',Html::encode($player->stripe_customer_id),Html::encode($player->username,$player->email)));
        }
      }
    }

}
