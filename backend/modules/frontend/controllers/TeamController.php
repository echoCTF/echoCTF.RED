<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\modules\frontend\models\Team;
use app\modules\frontend\models\TeamSearch;
use app\modules\frontend\models\TeamPlayer;
use app\modules\frontend\models\TeamPlayerSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * TeamController implements the CRUD actions for Team model.
 */
class TeamController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'toggle-academic' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Lists all Team models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new TeamSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Team model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $searchModel=new TeamPlayerSearch();
        $searchModel->team_id=$id;
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('view', [
            'dataProvider'=>$dataProvider,
            'searchModel'=>$searchModel,
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Team model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new Team();
        if(\app\modules\frontend\models\Player::find()->count() == 0)
        {
          // If there are no player redirect to create player page
          Yii::$app->session->setFlash('warning', "No Players found create one first.");
          return $this->redirect(['/frontend/player/create']);
        }
        $trans=Yii::$app->db->beginTransaction();

        try
        {
          if($model->load(Yii::$app->request->post()) && $model->save())
          {
              $model->refresh();
              $ts=new \app\modules\activity\models\TeamScore();
              $ts->team_id=$model->id;
              $ts->points=0;
              $ts->save();
              Yii::$app->db->createCommand("CALL repopulate_team_stream(:tid)")->bindValue(':tid',$model->id)->execute();
              $trans->commit();
              return $this->redirect(['view', 'id' => $model->id]);
          }
        }
        catch (\Exception $e)
        {
          $trans->rollBack();
          \Yii::$app->getSession()->setFlash('error', 'Failed to create team. '.$e->getMessage());
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Team model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model=$this->findModel($id);

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Team model.
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
     * Toggles an existing Team academic flag model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionToggleAcademic($id)
    {
        $model=$this->findModel($id);
        $model->updateAttributes(['academic' => !$model->academic]);
        return $this->redirect(Yii::$app->request->referrer ?? ['frontend/teamplayer/index']);
    }

    /**
     * Finds the Team model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Team the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(($model=Team::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
