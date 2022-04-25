<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\activity\models\Headshot;
use app\modules\activity\models\HeadshotSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * HeadshotController implements the CRUD actions for Headshot model.
 */
class HeadshotController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }

    /**
     * Lists all Headshot models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new HeadshotSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Headshot model.
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
     * Creates a new Headshot model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $submit=Yii::$app->request->post('submit');
        $model=new Headshot();
        if($submit && $submit[0]==='give') $this->give();
        elseif($submit && $submit[0]==='save' && $model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'player_id' => $model->player_id, 'target_id' => $model->target_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * Gives a Headshot for a target on a Player model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function give()
    {
      $model=new Headshot();
      if($model->load(Yii::$app->request->post()) && $model->validate())
      {
          if(intval($model->timer)===0)
          {
              $model->timer=random_int(240,10240);
          }
          Yii::$app->db->createCommand('CALL give_headshot(:player_id,:target_id,:timer)')
            ->bindValue(':player_id', $model->player_id)
            ->bindValue(':target_id', $model->target_id)
            ->bindValue(':timer', intval($model->timer))
            ->query();
          return $this->redirect(['view', 'player_id' => $model->player_id, 'target_id' => $model->target_id]);
      }
    }


    /**
     * Updates an existing Headshot model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $player_id
     * @param integer $target_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($player_id, $target_id)
    {
        $model=$this->findModel($player_id, $target_id);

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'player_id' => $model->player_id, 'target_id' => $model->target_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Headshot model.
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
     * Finds the Headshot model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $player_id
     * @param integer $target_id
     * @return Headshot the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($player_id, $target_id)
    {
        if(($model=Headshot::findOne(['player_id' => $player_id, 'target_id' => $target_id])) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
