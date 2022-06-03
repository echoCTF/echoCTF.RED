<?php
namespace app\modules\restapi\controllers;

use yii\rest\ActiveController;

class TargetController extends ActiveController
{
    public $modelClass='app\modules\gameplay\models\Target';

    public function actionAddToNetwork($id,$codename)
    {
      if(($network=\app\modules\gameplay\models\Network::findOne(['codename'=>$codename]))!==null)
      {
        $nt=new \app\modules\gameplay\models\NetworkTarget;
        $nt->network_id=$network->id;
        $nt->target_id=$id;
        if(!$nt->save())
          return false;
        return $nt->target->trigger($nt->target::EVENT_NEW_TARGET_ANNOUNCEMENT);
      }
    }
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
