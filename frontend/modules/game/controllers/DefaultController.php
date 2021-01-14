<?php

namespace app\modules\game\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

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
                'only' => ['rate-solver','rate-headshot'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['rate-solver','rate-headshot'],
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
    public function actionRateHeadshot($id)
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

    /**
     * Accepts a rating value from a player
     * @return string
     */
    public function actionRateSolver($id)
    {
      $solver=\app\modules\challenge\models\ChallengeSolver::findOne(['challenge_id'=>$id,'player_id'=>Yii::$app->user->id]);
      if($solver===null)
      {
        throw new NotFoundHttpException('You have not solved this challenge.');
      }
      if(Yii::$app->request->isPost && Yii::$app->request->post('rating')!==null)
      {
        $rating=(int)Yii::$app->request->post('rating');
        $solver->updateAttributes(['rating' => $rating]);
      }
    }

}
