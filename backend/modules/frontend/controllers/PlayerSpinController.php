<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\modules\frontend\models\PlayerSpin;
use app\modules\frontend\models\PlayerSpinSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PlayerSpinController implements the CRUD actions for PlayerSpin model.
 */
class PlayerSpinController extends Controller
{
    /**
     * {@inheritdoc}
     */
      public function behaviors()
      {
          return [
            'access' => [
                  'class' => \yii\filters\AccessControl::class,
                  'rules' => [
                      [
                          'allow' => true,
                          'roles' => ['@'],
                      ],
                  ],
              ],
              'verbs' => [
                  'class' => VerbFilter::class,
                  'actions' => [
                      'delete' => ['POST'],
                      'reset' => ['POST'],
                  ],
              ],
          ];
      }

    /**
     * Lists all PlayerSpin models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PlayerSpinSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PlayerSpin model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PlayerSpin model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PlayerSpin();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->player_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PlayerSpin model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->player_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PlayerSpin model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    /**
     * Resets all existing PlayerSpin models.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionReset(int $id=0)
    {
      $trans=Yii::$app->db->beginTransaction();
      try
      {
        if($id===0)
        {
          PlayerSpin::updateAll(['counter'=>0]);
        }
        elseif (($ps=PlayerSpin::findOne($id))!==null)
        {
          $ps->counter=0;
          $ps->save();
          $notif=new \app\modules\activity\models\Notification;
          $notif->player_id=$id;
          $notif->title="Restarts counter zeroed";
          $notif->body='<p>We have zeroed your restart counters. You can request restarts again.</p><p>If you need more restarts and you\'ve reached your maximum restarts for the day, come and find our moderators over at our <a href="https://discord.gg/gQuAdzz" title="echoCTF Discord Server" target="_blank">discord server</a>. We will be more than happy to reset the system for you or reset your counters.</p>';
          $notif->save();
        }
        $trans->commit();
        Yii::$app->session->setFlash('success','Player spin counters zeroed.');
      }
      catch (\Exception $e)
      {
        $trans->rollBack();
        Yii::$app->session->setFlash('error','Player spin counters failed to zero out.');
      }

      return $this->goBack(Yii::$app->request->referrer);
    }

    /**
     * Finds the PlayerSpin model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PlayerSpin the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PlayerSpin::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
