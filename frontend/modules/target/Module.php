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
        throw new NotFoundHttpException('You dont have access to this network target.');

      if(!$target->network->public && NetworkPlayer::findOne(['network_id'=>$target->network->id,'player_id'=>\Yii::$app->user->id]) === null)
        throw new NotFoundHttpException('You dont have access to this network target.');
    }

}
