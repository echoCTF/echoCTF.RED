<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Finding;
use app\modules\activity\models\PlayerFinding;
use app\modules\activity\models\PlayerFindingSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * PlayerfindingController implements the CRUD actions for PlayerFinding model.
 */
class PlayerFindingController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }

    /**
     * Lists all PlayerFinding models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new PlayerFindingSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PlayerFinding model.
     * @param integer $player_id
     * @param integer $finding_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($player_id, $finding_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($player_id, $finding_id),
        ]);
    }

    /**
     * Creates a new PlayerFinding model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new PlayerFinding();
        if(Player::find()->count() == 0)
        {
          // If there are no player redirect to create player page
          Yii::$app->session->setFlash('warning', Yii::t('app',"No Players found create one first."));
          return $this->redirect(['/frontend/player/create']);
        }
        if(Finding::find()->count() == 0)
        {
          // If there are no questions redirect to create question
          Yii::$app->session->setFlash('warning', Yii::t('app',"No Finding found create one first."));
          return $this->redirect(['/gameplay/finding/create']);
        }

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'player_id' => $model->player_id, 'finding_id' => $model->finding_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PlayerFinding model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $player_id
     * @param integer $finding_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($player_id, $finding_id)
    {
        $model=$this->findModel($player_id, $finding_id);

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'player_id' => $model->player_id, 'finding_id' => $model->finding_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PlayerFinding model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $player_id
     * @param integer $finding_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($player_id, $finding_id)
    {
        $this->findModel($player_id, $finding_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PlayerFinding model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $player_id
     * @param integer $finding_id
     * @return PlayerFinding the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($player_id, $finding_id)
    {
        if(($model=PlayerFinding::findOne(['player_id' => $player_id, 'finding_id' => $finding_id])) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app','The requested page does not exist.'));
    }
}
