<?php

namespace app\modules\smartcity\controllers;

use yii\helpers\ArrayHelper;

/**
 * Default controller for the `smartcity` module
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
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
