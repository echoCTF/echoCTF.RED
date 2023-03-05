<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Treasure;
use app\modules\activity\models\PlayerTreasure;
use app\modules\activity\models\PlayerTreasureSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * PlayerTreasureController implements the CRUD actions for PlayerTreasure model.
 */
class PlayerTreasureController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }

    /**
     * Lists all PlayerTreasure models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new PlayerTreasureSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PlayerTreasure model.
     * @param integer $player_id
     * @param integer $treasure_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($player_id, $treasure_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($player_id, $treasure_id),
        ]);
    }

    /**
     * Creates a new PlayerTreasure model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new PlayerTreasure();
        if(Player::find()->count() == 0)
        {
          // If there are no player redirect to create player page
          Yii::$app->session->setFlash('warning', Yii::t('app',"No Players found create one first."));
          return $this->redirect(['/frontend/player/create']);
        }
        if(Treasure::find()->count() == 0)
        {
          // If there are no questions redirect to create question
          Yii::$app->session->setFlash('warning', Yii::t('app',"No Treasures found create one first."));
          return $this->redirect(['/gameplay/treasure/create']);
        }

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'player_id' => $model->player_id, 'treasure_id' => $model->treasure_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PlayerTreasure model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $player_id
     * @param integer $treasure_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($player_id, $treasure_id)
    {
        $model=$this->findModel($player_id, $treasure_id);

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'player_id' => $model->player_id, 'treasure_id' => $model->treasure_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PlayerTreasure model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $player_id
     * @param integer $treasure_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($player_id, $treasure_id)
    {
        $this->findModel($player_id, $treasure_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PlayerTreasure model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $player_id
     * @param integer $treasure_id
     * @return PlayerTreasure the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($player_id, $treasure_id)
    {
        if(($model=PlayerTreasure::findOne(['player_id' => $player_id, 'treasure_id' => $treasure_id])) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app','The requested page does not exist.'));
    }
}
