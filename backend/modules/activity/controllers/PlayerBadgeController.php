<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Badge;
use app\modules\activity\models\PlayerBadge;
use app\modules\activity\models\PlayerBadgeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PlayerbadgeController implements the CRUD actions for PlayerBadge model.
 */
class PlayerBadgeController extends Controller
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
                 ],
             ],
         ];
     }

    /**
     * Lists all PlayerBadge models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PlayerBadgeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PlayerBadge model.
     * @param integer $player_id
     * @param integer $badge_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($player_id, $badge_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($player_id, $badge_id),
        ]);
    }

    /**
     * Creates a new PlayerBadge model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PlayerBadge();
        if(Player::find()->count()==0)
        {
          // If there are no player redirect to create player page
          Yii::$app->session->setFlash('warning', "No Players found create one first.");
          return $this->redirect(['/frontend/player/create']);
        }
        if(Badge::find()->count()==0) {
          // If there are no questions redirect to create question
          Yii::$app->session->setFlash('warning', "No Badges found create one first.");
          return $this->redirect(['/gameplay/badge/create']);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'player_id' => $model->player_id, 'badge_id' => $model->badge_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PlayerBadge model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $player_id
     * @param integer $badge_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($player_id, $badge_id)
    {
        $model = $this->findModel($player_id, $badge_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'player_id' => $model->player_id, 'badge_id' => $model->badge_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PlayerBadge model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $player_id
     * @param integer $badge_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($player_id, $badge_id)
    {
        $this->findModel($player_id, $badge_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PlayerBadge model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $player_id
     * @param integer $badge_id
     * @return PlayerBadge the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($player_id, $badge_id)
    {
        if (($model = PlayerBadge::findOne(['player_id' => $player_id, 'badge_id' => $badge_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
