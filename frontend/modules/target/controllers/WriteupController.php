<?php

namespace app\modules\target\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\base\InvalidArgumentException;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use app\modules\game\models\Headshot;
use app\modules\target\models\Target;
use app\modules\target\models\PlayerTargetHelp as PTH;
use app\modules\target\models\Writeup;
use app\overloads\yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\modules\game\models\WriteupRating;

/**
 * Writeup controller for the `target` module
 */
class WriteupController extends \app\components\BaseController
{

      public function behaviors()
      {
          return ArrayHelper::merge(parent::behaviors(),[
              'access' => [
                  'class' => AccessControl::class,
                  'only' => ['enable','submit','view','update','read'],
                  'rules' => [
                      [
                          'allow' => true,
                          'actions' => ['submit','update'],
                          'roles' => ['@'],
                          'verbs'=>['post','get'],
                      ],
                      [
                          'allow' => true,
                          'actions' => ['enable'],
                          'roles' => ['@'],
                          'verbs'=>['post'],
                      ],
                      [
                          'allow' => true,
                          'actions' => ['view','read'],
                          'roles' => ['@'],
                      ],
                      [
                          'allow' => false,
                      ],

                  ],
              ],
          ]);
      }


    /**
     * View your own writeup for a given target
     * @return string
     */
     public function actionView(int $id)
     {
         return $this->render('view', [
             'model' => $this->findModel(Yii::$app->user->id, $id),
         ]);
     }

     /**
      * Read a given writeup for a target
      * @return string
      */
      public function actionRead(int $target_id, int $id)
      {
          if(!PTH::findOne(['player_id'=>Yii::$app->user->id,'target_id'=>$target_id]) && !Headshot::findOne(['target_id'=>$target_id,'player_id'=>Yii::$app->user->id]))
          {
            Yii::$app->session->setFlash('error', \Yii::t('app','You are not allowed to read writeups for this target.'));
            return $this->redirect(['default/view','id'=>$target_id]);
          }

          $model=$this->findModelId(['id'=>$id,'target_id'=>$target_id,'approved'=>true]);
          if (($rating=WriteupRating::findOne(['player_id'=>Yii::$app->user->id, 'writeup_id'=>$id]))===null)
          {
            $rating=new WriteupRating;
            $rating->writeup_id=$id;
            $rating->player_id=Yii::$app->user->id;
          }

          return $this->render('read', [
              'model' => $model,
              'rating'=> $rating,
          ]);
      }

    /**
     * Submit a writeup on a the given target
     * @return Response|string
     */
    public function actionSubmit(int $id)
    {
      $target=Target::findOne($id);
      if($target && !$target->writeup_allowed)
      {
        Yii::$app->session->setFlash('warning', \Yii::t('app','Writeups are not allowed for this target.'));
        return $this->redirect(['default/view','id'=>$id]);
      }


      $headshot=Headshot::findOne(['target_id'=>$id,'player_id'=>Yii::$app->user->id]);
      if($headshot===null)
      {
        throw new NotFoundHttpException(\Yii::t('app','You dont have a headshot for the given target.'));
      }

      if($headshot->writeup!==null)
      {
        Yii::$app->session->setFlash('error', \Yii::t('app','You have already submitted a writeup for this target.'));
        return $this->redirect(['default/view','id'=>$id]);
      }

      $model = new Writeup(['scenario'=>Writeup::SCENARIO_SUBMIT]);
      if ($model->load(Yii::$app->request->post())) {
          $model->player_id=Yii::$app->user->id;
          $model->target_id=$id;
          $model->approved=0;
          $model->status='PENDING';
          if($model->save())
          {
            Yii::$app->session->setFlash('success', \Yii::t('app','Thank you for your submission. Your writeup has been saved. A member of our staff will review and approve or reject.'));
            return $this->redirect(['view', 'id' => $id]);
          }
          Yii::$app->session->setFlash('error', \Yii::t('app','Failed to save writeup, something went wrong.'));
      }

      return $this->render('create', [
          'model' => $model,
          'headshot'=>$headshot,
      ]);



    }

    /**
     * Updates an existing Writeup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $player_id
     * @param integer $target_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel(Yii::$app->user->id, $id);
        $oldmodel = $this->findModel(Yii::$app->user->id, $id);
        $oldmodel->scenario=Writeup::SCENARIO_SUBMIT;
        $model->scenario=Writeup::SCENARIO_SUBMIT;
        if ($model->load(Yii::$app->request->post()) && $model->content!==$oldmodel->content )
        {
          $oldmodel->status='PENDING';
          $oldmodel->content=$model->content;
          if($oldmodel->save())
          {
            Yii::$app->session->setFlash('success', \Yii::t('app','The writeup has been updated.'));
            return $this->redirect(['view', 'id' => $id]);
          }
          Yii::$app->session->setFlash('error', \Yii::t('app','Failed to update writeup, something went wrong.'));
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Enables a writeups for the player on a the given target
     * @return Response|string
     */
    public function actionEnable(int $id)
    {
        $writeups=Writeup::find()->where(['target_id'=>$id]);
        $player_headshots=intval(Headshot::find()->where(['player_id'=>Yii::$app->user->id])->count());
        $player_writeups=intval(PTH::find()->where(['player_id'=>Yii::$app->user->id])->count());
        if($player_writeups>=($player_headshots+2))
        {
          Yii::$app->session->setFlash('error', \Yii::t('app','You have activated too many writeups, headshot some of the targets first.'));
          return $this->redirect(['default/view','id'=>$id]);
        }
        if((int)$writeups->count()===0)
        {
          Yii::$app->session->setFlash('error', \Yii::t('app','There are no writeups for this target.'));
          return $this->redirect(['default/view','id'=>$id]);
        }

        if(PTH::findOne(['player_id'=>Yii::$app->user->id,'target_id'=>$id])!==null)
        {
          Yii::$app->session->setFlash('error', \Yii::t('app','You have already enabled writeups for this target.'));
          return $this->redirect(['default/view','id'=>$id]);
        }

        try {
          $this->module->checkNetwork($writeups->one()->target);
        }
        catch(\Throwable $e)
        {
          Yii::$app->session->setFlash('error', \Yii::t('app',"Failed to activate writeups for this target. You don't have access to this network."));
          return $this->redirect(['default/view','id'=>$id]);
        }

        $connection=Yii::$app->db;
        $transaction=$connection->beginTransaction();
        try
        {
          $pth=new PTH;
          $pth->player_id=Yii::$app->user->id;
          $pth->target_id=$id;
          $pth->created_at=new \yii\db\Expression('NOW()');
          $pth->save(false);
          $transaction->commit();
          Yii::$app->session->setFlash('success', \Yii::t('app','You have successfully activated writeups for this target.'));
        }
        catch(\Exception $e)
        {
          $transaction->rollBack();
          Yii::$app->session->setFlash('error', \Yii::t('app','Failed to activate writeups for this target.'));
          throw $e;
        }
        return $this->redirect(Url::previous());
    }

    /**
     * Finds the Writeup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $player_id
     * @param integer $target_id
     * @return Writeup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $player_id,int $target_id)
    {
        if(($model=Writeup::findOne(['player_id'=>$player_id,'target_id'=>$target_id])) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(\Yii::t('app','The requested writeup does not exist.'));
    }

    /**
     * Finds the Writeup model based on its id value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Writeup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelId($id)
    {
        if(($model=Writeup::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(\Yii::t('app','The requested writeup does not exist.'));
    }


    protected function findProfile($id)
    {
        if(($model=\app\models\Profile::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(\Yii::t('app','The requested profile does not exist.'));
    }

}
