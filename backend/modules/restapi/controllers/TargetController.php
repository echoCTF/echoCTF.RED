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

    public function actionDownload(int $id)
    {
      if(($model=\app\modules\gameplay\models\Target::findOne($id))!==null && file_exists(\Yii::getAlias('@web/images/targets/'.$model->avatar)))
        return \Yii::$app->response->sendFile(\Yii::getAlias('@web/images/targets/'.$model->avatar));
    }

}
