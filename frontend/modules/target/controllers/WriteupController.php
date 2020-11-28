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
use app\modules\target\models\PlayerTargetHelp;
use app\modules\target\models\Writeup;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

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
                  'only' => ['enable','submit','view','update'],
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
                          'roles'=>['@']
                      ],
                  ],
              ],
          ]);
      }


    /**
     * Submit a writeup on a the given target
     * @return string
     */
     public function actionView(int $id)
     {
         return $this->render('view', [
             'model' => $this->findModel(Yii::$app->user->id, $id),
         ]);
     }
    /**
     * Submit a writeup on a the given target
     * @return Response|string
     */
    public function actionSubmit(int $id)
    {
      $headshot=Headshot::findOne(['target_id'=>$id,'player_id'=>Yii::$app->user->id]);
      if($headshot===NULL)
      {
        throw new NotFoundHttpException('You dont have a headshot for the given target.');
      }

      if($headshot->writeup!==null)
      {
        Yii::$app->session->setFlash('error', 'You have already submitted a writeup for this target.');
        return $this->redirect(['default/index','id'=>$id]);
      }

      $model = new Writeup();
      if ($model->load(Yii::$app->request->post())) {
          $model->player_id=Yii::$app->user->id;
          $model->target_id=$id;
          $model->approved=0;
          $model->status='PENDING';
          if($model->save())
          {
            Yii::$app->session->setFlash('success', 'Thank you for your submittion. Your writeup has been saved. A member of our staff will review and approve or reject.');
            return $this->redirect(['view', 'id' => $id]);
          }
          Yii::$app->session->setFlash('error', 'Failed to save writeup, something went wrong.');
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
        $oldmodel=$model;
        if ($model->load(Yii::$app->request->post()))
        {
          $oldmodel->status='PENDING';
          $oldmodel->content=$model->content;
          if($oldmodel->save())
          {
            Yii::$app->session->setFlash('success', 'The writeup has been updated.');
            return $this->redirect(['view', 'id' => $id]);
          }
          Yii::$app->session->setFlash('error', 'Failed to update writeup, something went wrong.');
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
        if((int)$writeups->count()===0)
        {
          Yii::$app->session->setFlash('error', 'There are no writeups for this target.');
          return $this->redirect(['default/index','id'=>$id]);
        }

        if(PlayerTargetHelp::findOne(['player_id'=>Yii::$app->user->id,'target_id'=>$id])!==null)
        {
          Yii::$app->session->setFlash('error', 'You have already enabled writeups for this target.');
          return $this->redirect(['default/index','id'=>$id]);
        }

        $connection=Yii::$app->db;
        $transaction=$connection->beginTransaction();
        try
        {
          $pth=new PlayerTargetHelp;
          $pth->player_id=Yii::$app->user->id;
          $pth->target_id=$id;
          $pth->save(false);
          $transaction->commit();
          Yii::$app->session->setFlash('success', 'You have successfully activated writeups for this target.');
        }
        catch(\Exception $e)
        {
          $transaction->rollBack();
          Yii::$app->session->setFlash('error', 'Failed to activate writeups for this target.');
          throw $e;
        }
        return $this->redirect(['default/index','id'=>$id]);
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

        throw new NotFoundHttpException('The requested writeup does not exist.');
    }
    protected function findProfile($id)
    {
        if(($model=\app\models\Profile::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested profile does not exist.');
    }

}
