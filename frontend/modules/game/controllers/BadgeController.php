<?php

namespace app\modules\game\controllers;

use Yii;
use yii\web\Controller;
use app\overloads\yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use app\modules\game\models\Headshot;
/**
 * Badge controller for the `game` module
 */
class BadgeController extends \app\components\BaseController
{
    public $layout = '//badge';
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                ],
            ],
        ]);
    }

    public function actionHeadshot($profile_id,$target_id)
    {
      $profile=\app\models\Profile::findOne($profile_id);
      $headshot=Headshot::findOne(['player_id'=>$profile->player_id,'target_id'=>$target_id]);
      $topH=Headshot::find()->select('player_id')->where(['target_id'=>$target_id])->orderBy(['timer'=>SORT_ASC])->limit(10)->asArray()->all();
      if($profile==null || $headshot==null)
       return $this->redirect(['/']);

      return $this->render('index',[
        'headshot'=>$headshot,
        'top'=>ArrayHelper::getColumn($topH,'player_id'),
      ]);
    }

}
