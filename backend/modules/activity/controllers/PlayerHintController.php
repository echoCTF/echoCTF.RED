<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Hint;
use app\modules\activity\models\PlayerHint;
use app\modules\activity\models\PlayerHintSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * PlayerhintController implements the CRUD actions for PlayerHint model.
 */
class PlayerHintController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }

    /**
     * Lists all PlayerHint models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new PlayerHintSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PlayerHint model.
     * @param integer $player_id
     * @param integer $hint_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($player_id, $hint_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($player_id, $hint_id),
        ]);
    }

    /**
     * Creates a new PlayerHint model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new PlayerHint();
        if(Player::find()->count() == 0)
        {
          // If there are no player redirect to create player page
          Yii::$app->session->setFlash('warning', Yii::t('app',"No Players found create one first."));
          return $this->redirect(['/frontend/player/create']);
        }
        if(Hint::find()->count() == 0)
        {
          // If there are no questions redirect to create question
          Yii::$app->session->setFlash('warning', Yii::t('app',"No Hints found create one first."));
          return $this->redirect(['/gameplay/hint/create']);
        }

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'player_id' => $model->player_id, 'hint_id' => $model->hint_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PlayerHint model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $player_id
     * @param integer $hint_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($player_id, $hint_id)
    {
        $model=$this->findModel($player_id, $hint_id);

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'player_id' => $model->player_id, 'hint_id' => $model->hint_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PlayerHint model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $player_id
     * @param integer $hint_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($player_id, $hint_id)
    {
        $this->findModel($player_id, $hint_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PlayerHint model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $player_id
     * @param integer $hint_id
     * @return PlayerHint the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($player_id, $hint_id)
    {
        if(($model=PlayerHint::findOne(['player_id' => $player_id, 'hint_id' => $hint_id])) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app','The requested page does not exist.'));
    }
}
