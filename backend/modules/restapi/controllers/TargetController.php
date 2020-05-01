<?php
namespace app\modules\restapi\controllers;

use yii\rest\ActiveController;

class TargetController extends ActiveController
{
    public $modelClass='app\modules\gameplay\models\Target';
    public function actionGetByIp($ip)
    {
      return \app\modules\gameplay\models\Target::find()->andWhere(['ip'=>ip2long($ip)])->one();
    }
}
