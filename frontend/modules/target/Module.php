<?php

namespace app\modules\target;

use app\modules\network\models\NetworkPlayer;
use \yii\web\NotFoundHttpException;

/**
 * target module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace='app\modules\target\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function checkNetwork($target)
    {
      if($target->network === null)
      {
        return;
      }

      if(!$target->network->active)
        throw new NotFoundHttpException('This target belongs to a network that is not active.');

      if(!$target->network->public && NetworkPlayer::findOne(['network_id'=>$target->network->id,'player_id'=>\Yii::$app->user->id]) === null)
        throw new NotFoundHttpException('This target belongs to a network you don\'t have access.');
    }

}
