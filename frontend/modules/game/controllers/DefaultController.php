<?php

namespace app\modules\game\controllers;

use Yii;
use yii\web\Controller;
use app\overloads\yii\filters\AccessControl;
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
                'only' => ['rate-solver','rate-headshot','rate-writeup'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['rate-solver','rate-headshot','rate-writeup'],
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
        throw new NotFoundHttpException(\Yii::t('app',"You don't have a headshot for the given target."));
      }
      if(Yii::$app->request->isPost && Yii::$app->request->post('rating')!==null)
      {
        $rating=(int)Yii::$app->request->post('rating');
        if($rating>-1 && $rating<=6)
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
        throw new NotFoundHttpException(\Yii::t('app','You have not solved this challenge.'));
      }
      if(Yii::$app->request->isPost && Yii::$app->request->post('rating')!==null)
      {
        $rating=(int)Yii::$app->request->post('rating');
        if($rating>-1 && $rating<=6)
          $solver->updateAttributes(['rating' => $rating]);
      }
    }

    /**
     * Accepts a rating value from a player
     * @return string
     */
    public function actionRateWriteup($target_id,$id)
    {
      $writeup=\app\modules\target\models\Writeup::findOne(['id'=>$id]);
      if($writeup===null)
      {
        throw new NotFoundHttpException(\Yii::t('app','No such writeup exist.'));
      }
      $headshot=\app\modules\game\models\Headshot::findOne(['target_id'=>$writeup->target_id,'player_id'=>Yii::$app->user->id]);
      $PTH=\app\modules\target\models\PlayerTargetHelp::findOne(['target_id'=>$writeup->target_id,'player_id'=>Yii::$app->user->id]);

      if($headshot===null && $PTH===null)
      {
        throw new NotFoundHttpException(\Yii::t('app','No such writeup exist.'));
      }

      if (($WR=\app\modules\game\models\WriteupRating::findOne(['player_id'=>Yii::$app->user->id, 'writeup_id'=>$id]))===null)
      {
        $WR=new \app\modules\game\models\WriteupRating;
        $WR->writeup_id=$id;
        $WR->player_id=Yii::$app->user->id;
      }
      if(Yii::$app->request->isPost && Yii::$app->request->post('rating')!==null)
      {
        $rating=(int)Yii::$app->request->post('rating');
        if ($rating>0 && $rating<=5)
        {
          if($WR->isNewRecord)
          {
            $WR->rating=$rating;
            $WR->save();
          }
          else
          {
            $WR->updateAttributes(['rating' => $rating]);
          }
        }
      }
    }

}
