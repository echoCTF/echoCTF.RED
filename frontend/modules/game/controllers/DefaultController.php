<?php

namespace app\modules\game\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * Default controller for the `game` module
 */
class DefaultController extends \app\components\BaseController
{

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
            'access' => [
                'class' => AccessControl::class,
                'only' => ['rate'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['rate'],
                        'roles' => ['@'],
                        'verbs'=>['post'],
                    ],
                ],
            ],
        ]);
    }



    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Accepts a rating value from a player
     * @return string
     */
    public function actionRate($id)
    {
      $headshot=\app\modules\game\models\Headshot::findOne(['target_id'=>$id,'player_id'=>Yii::$app->user->id]);
      if($headshot===null)
      {
        throw new NotFoundHttpException('You dont have a headshot for the given target.');
      }
      if(Yii::$app->request->isPost && Yii::$app->request->post('rating')!==null)
      {
        $rating=(int)Yii::$app->request->post('rating');
        $headshot->updateAttributes(['rating' => $rating]);
      }
    }


}
