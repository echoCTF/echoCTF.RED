<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\activity\models\Notification;
use app\modules\activity\models\Writeup;
use app\modules\activity\models\WriteupSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WriteupController implements the CRUD actions for Writeup model.
 */
class WriteupController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'approve' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Writeup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WriteupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Writeup model.
     * @param integer $player_id
     * @param integer $target_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($player_id, $target_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($player_id, $target_id),
        ]);
    }

    /**
     * Creates a new Writeup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Writeup();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'player_id' => $model->player_id, 'target_id' => $model->target_id]);
        }

        return $this->render('create', [
            'model' => $model,
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
    public function actionUpdate(int $player_id,int $target_id)
    {
        $model = $this->findModel($player_id, $target_id);
        $oldmodel=$model->attributes;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if($oldmodel['status'] !== $model->status)
            {
              $notif=new Notification;
              $notif->player_id=$player_id;
              $notif->archived=0;
              $notif->title=$notif->body=sprintf("The status of the writeup for [%s/%s], has changed to [%s].",$model->target->name,$model->target->ipoctet,$model->status);
              $notif->save();
            }
            return $this->redirect(['view', 'player_id' => $model->player_id, 'target_id' => $model->target_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Writeup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $player_id
     * @param integer $target_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($player_id, $target_id)
    {
        $this->findModel($player_id, $target_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Approves an existing Writeup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $player_id
     * @param integer $target_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionApprove($player_id, $target_id)
    {
        $model=$this->findModel($player_id, $target_id);
        $model->approved=true;
        $model->status='OK';
        $model->comment=null;
        if($model->save())
        {
          $notif=new Notification;
          $notif->player_id=$player_id;
          $notif->body=$notif->title=sprintf("The writeup you submitted for %s/%s has been approved. Thank you!",$model->target->name,$model->target->ipoctet);
          $notif->archived=0;
          $notif->save();
          Yii::$app->session->setFlash('success','Writeup approved.');
        }
        else {
          Yii::$app->session->setFlash('error','Failed to approve writeup.');

        }
        return $this->redirect(['index']);
    }



    /**
     * Finds the Writeup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $player_id
     * @param integer $target_id
     * @return Writeup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($player_id, $target_id)
    {
        if (($model = Writeup::findOne(['player_id' => $player_id, 'target_id' => $target_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
