<?php

namespace app\modules\sales\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "product".
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property int $active
 * @property int $livemode
 * @property string|null $metadata
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'name','price_id'], 'required'],
            [['description', 'metadata','htmlOptions','perks','interval','price_id'], 'string'],
            [['weight','interval_count'],'integer'],
            [['active', 'livemode'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['id','shortcode'], 'string', 'max' => 40],
            [['name','price_id'], 'string', 'max' => 255],
            [['currency'],'default','value'=>'eur'],
            [['weight'],'default','value'=>0],
            [['interval_count'],'default','value'=>'12'],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'active' => Yii::t('app', 'Active'),
            'livemode' => Yii::t('app', 'Livemode'),
            'metadata' => Yii::t('app', 'Metadata'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
    /**
     * Gets query for [[Network]].
     *
     * @return \yii\db\ActiveQuery|NetworkQuery
     */
    public function getProductNetworks()
    {
        return $this->hasMany(ProductNetwork::class, ['id' => 'product_id']);
    }

    /**
     * {@inheritdoc}
     * @return ProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductQuery(get_called_class());
    }

    /**
     * Gets all Product from Stripe and syncs with current ones.
     * @return mixed
     */
    public static function FetchStripe()
    {
      $stripe = new \Stripe\StripeClient(\Yii::$app->sys->stripe_apiKey);
      $products=$stripe->products->all(['active'=>true]);
      foreach($products->data as $stripeProduct)
      {
        $product=Product::findOne($stripeProduct->id);

        if($product===null)
        {
          $product=new Product();
          $product->id=$stripeProduct->id;
          $product->created_at=new \yii\db\Expression('NOW()');
        }
        $product->active=$stripeProduct->active;
        $product->name=$stripeProduct->name;
        $product->description=$stripeProduct->description;
        $product->livemode=$stripeProduct->livemode;
        $prices=$stripe->prices->all(['product'=>$product->id]);
        $price=$prices->data[0];

        if(isset($price->recurring) && $price->recurring->interval)
          $product->interval=$price->recurring->interval;
        else
          $product->interval='day';

        if(isset($price->recurring) && $price->recurring->interval_count)
          $product->interval_count=$price->recurring->interval_count;
        else
          $product->interval_count=1;

        $product->price_id=$price->id;
        $product->currency=$price->currency;
        $product->metadata=json_encode($stripeProduct->metadata);
        $product->unit_amount=$price->unit_amount;
        $product->updated_at=new \yii\db\Expression('NOW()');
        if($stripeProduct->metadata->htmlOptions)
          $product->htmlOptions=trim($stripeProduct->metadata->htmlOptions);

        if($stripeProduct->metadata->shortcode)
          $product->shortcode=trim($stripeProduct->metadata->shortcode);

        if($stripeProduct->metadata->perks)
          $product->perks=trim($stripeProduct->metadata->perks);

        if($stripeProduct->metadata->weight)
          $product->weight=intval(trim($stripeProduct->metadata->weight));

        if(!$product->save())
        {
          if(\Yii::$app instanceof \yii\console\Application)
            printf("Failed to save product: %s, %s\n",$stripeProduct->id,$stripeProduct->name);
          else
            \Yii::$app->session->addFlash('error', sprintf('Failed to save product: %s, %s',Html::encode($stripeProduct->id),Html::encode($stripeProduct->name)));
        }
        else
        {
          if(\Yii::$app instanceof \yii\console\Application)
            printf("Imported product: %s, %s\n",$stripeProduct->id,$stripeProduct->name);
          else
            \Yii::$app->session->addFlash('success', sprintf('Imported product: %s, %s',Html::encode($stripeProduct->id),Html::encode($stripeProduct->name)));
        }
        if(!empty($stripeProduct->metadata->network_ids))
        {
          foreach(explode(',',$stripeProduct->metadata->network_ids) as $nid)
          {
            if(ProductNetwork::findOne(['product_id'=>$product->id, 'network_id'=>$nid])===null)
            {
              $pn=new ProductNetwork;
              $pn->product_id=$product->id;
              $pn->network_id=$nid;
              $pn->save();
            }
          }
        }
      }
    }
}
