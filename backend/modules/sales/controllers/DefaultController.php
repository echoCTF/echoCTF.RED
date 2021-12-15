<?php

namespace app\modules\sales\controllers;

use Yii;
use yii\web\Controller;
use app\modules\sales\models\PlayerCustomerSearch;
use yii\helpers\ArrayHelper;

/**
 * Default controller for the `sales` module
 */
class DefaultController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
   public function behaviors()
   {
     return ArrayHelper::merge(parent::behaviors(),[]);
   }

    /**
     * Renders the index / semi-dashboard view for the sales module
     * @return string
     */
    public function actionIndex()
    {
      return $this->render('index');
    }

}
