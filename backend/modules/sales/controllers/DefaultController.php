<?php

namespace app\modules\sales\controllers;

use Yii;
use yii\web\Controller;
use app\modules\sales\models\PlayerCustomerSearch;
/**
 * Default controller for the `sales` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index / semi-dashboard view for the sales module
     * @return string
     */
    public function actionIndex()
    {      
      return $this->render('index');
    }

}
