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
  public $controllerNamespace = 'app\modules\target\controllers';

  /**
   * {@inheritdoc}
   */
  public function init()
  {
    parent::init();

    // custom initialization code goes here
  }
}
