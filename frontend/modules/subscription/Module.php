<?php

namespace app\modules\subscription;
use yii\base\BootstrapInterface;
use yii\helpers\ArrayHelper;
/**
 * challenge module definition class
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
   public $prices;
   public $priceIds;
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace='app\modules\subscription\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        \Yii::configure($this, require __DIR__ . '/config/main.php');
    }

    public function bootstrap($app)
    {
      if ($app instanceof \yii\web\Application) {
        \Yii::configure($this, require __DIR__ . '/config/web.php');
        $app->getUrlManager()->addRules($this->components['urlManager']['rules'], false);
      }
      elseif ($app instanceof \yii\console\Application)
      {
          \Yii::configure($this, require __DIR__ . '/config/console.php');
          $this->controllerNamespace = 'app\modules\subscription\commands';
          $app->controllerMap=ArrayHelper::merge($app->controllerMap, $this->controllerMap);
      }
    }


    public function getExists()
    {
      if(\app\modules\subscription\models\PlayerSubscription::findOne(\Yii::$app->user->id)!==null || \Yii::$app->sys->all_players_vip===true)
      {
        return true;
      }
      return false;
    }
    public function getExpires()
    {

      $model=\app\modules\subscription\models\PlayerSubscription::findOne(\Yii::$app->user->id);
      if(!$model)
      {
        return null;
      }
      $expiration =  new \DateTime($model->ending);
      $current =  new \DateTime(date("Y-m-d H:i:s"));

      $interval = $expiration->diff($current)->days;
      if($interval===0)
        return $expiration->diff($current)->format('%H:%I:%S');
      return $interval.' day'.($interval>0 ? 's' : '');
    }

    public function getIsActive()
    {
      if(intval(\app\modules\subscription\models\PlayerSubscription::find()->me()->active()->notExpired()->count())>0 || \Yii::$app->sys->all_players_vip===true)
      {
        return true;
      }
      return false;
    }

    public function getProduct()
    {

      $model=\app\modules\subscription\models\PlayerSubscription::findOne(\Yii::$app->user->id);
      if(!$model)
      {
        return null;
      }
      return $model->product;
    }

    public function getPortalButton($view,$button='<button class="btn btn-block btn-info font-weight-bold">Manage Billing</button>')
    {
      $form='<form id="manage-billing-form">'.$button.'</form>';
      $view->registerJs('const manageBillingForm = document.querySelector("#manage-billing-form");
          manageBillingForm.addEventListener("submit", function(e) {
            e.preventDefault();
            fetch("'.\yii\helpers\Url::to(['/subscription/default/redirect-customer-portal']).'", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({
                "'.\Yii::$app->request->csrfParam.'": "'.\Yii::$app->request->csrfToken.'"
              }),
            })
              .then((response) => response.json())
              .then((data) => {
                window.location.href = data.url;
              })
              .catch((error) => {
                console.error("Error:", error);
              });
          });
      ');
      return $form;
    }
    public static function getPortalLink($view,$link)
    {
      $view->registerJs('$("#stripePortal").on("click", function(e) {
            e.preventDefault();
            fetch("'.\yii\helpers\Url::to(['/subscription/default/redirect-customer-portal']).'", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({
                "'.\Yii::$app->request->csrfParam.'": "'.\Yii::$app->request->csrfToken.'"
              }),
            })
              .then((response) => response.json())
              .then((data) => {
                window.location.href = data.url;
              })
              .catch((error) => {
                console.error("Error:", error);
              });
          });
      ');
      return $link;
    }

}
