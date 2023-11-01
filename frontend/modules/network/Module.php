<?php

namespace app\modules\network;

use app\modules\network\models\NetworkPlayer;

/**
 * network module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace='app\modules\network\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function checkTarget($target)
    {
      if($target->network === null)
        return false;

      if(!$target->network->active)
        return false;

      if(!$target->network->public && NetworkPlayer::findOne(['network_id'=>$target->network->id,'player_id'=>\Yii::$app->user->id]) === null)
        return false;
      return true;
    }

    public function checkNetwork($network)
    {
      if($network === null)
        return false;

      // check network is not active
      if(!$network->active)
        return false;

      // check network is not public and user has no access to it or network id does not exist in product_networks
      if(!$network->public && NetworkPlayer::findOne(['network_id'=>$network->id,'player_id'=>\Yii::$app->user->id]) === null)
        return false;

      return true;
    }

}
