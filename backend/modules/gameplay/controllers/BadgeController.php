<?php

namespace app\modules\gameplay\controllers;

use Yii;
use app\modules\gameplay\models\Badge;
use app\modules\gameplay\models\BadgeSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * BadgeController implements the CRUD actions for Badge model.
 */
class BadgeController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }

    /**
     * Lists all Badge models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new BadgeSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Badge model.
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
     * Creates a new Badge model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new Badge();
        $transaction=\Yii::$app->db->beginTransaction();
        try
        {
          if($model->load(Yii::$app->request->post()) && $model->save())
          {
            $treasures=Yii::$app->request->post()['Badge']['treasures'];
            $findings=Yii::$app->request->post()['Badge']['findings'];
            $this->addBadgeFindings($findings,$model);
            $this->addBadgeTreasures($treasures,$model);
            $transaction->commit();
            Yii::$app->session->setFlash('success', Yii::t('app',"Badge created with success"));
            return $this->redirect(['view', 'id' => $model->id]);
          }
        }
        catch(\Exception $e)
        {
          $transaction->rollback();
          Yii::$app->session->setFlash('error', Yii::t('app',"Failed to create badge"));
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Badge model.
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

            $findings=Yii::$app->request->post()['Badge']['findings'];
            $treasures=Yii::$app->request->post()['Badge']['treasures'];
            if(is_array($findings))
            {
              // process badge_findings
            }
            if(is_array($treasures))
            {
              //do treasures;
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Badge model.
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
     * Finds the Badge model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Badge the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(($model=Badge::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app','The requested page does not exist.'));
    }

    protected function addBadgeTreasures($treasures,$model)
    {
      if(is_array($treasures))
      {
        foreach($treasures as $id)
        {
          $bt=new \app\modules\gameplay\models\BadgeTreasure;
          $bt->badge_id=$model->id;
          $bt->treasure_id=$id;
          $bt->save();
        }
      }
    }
    protected function addBadgeFindings($findings,$model)
    {
      if(is_array($findings))
      {
        foreach($findings as $id)
        {
          $bf=new \app\modules\gameplay\models\BadgeFinding;
          $bf->badge_id=$model->id;
          $bf->finding_id=$id;
          $bf->save();
        }
      }
    }

}
