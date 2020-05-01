<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\activity\models\Headshot;
use app\modules\activity\models\HeadshotSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * HeadshotController implements the CRUD actions for Headshot model.
 */
class HeadshotController extends Controller
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
     * Lists all Headshot models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new HeadshotSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
        $model = new Headshot();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
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
    public function actionGive()
    {
      $model = new Headshot();

      if ($model->load(Yii::$app->request->post()) && $model->validate()) {
          Yii::$app->db->createCommand('insert ignore into player_finding (player_id,finding_id) select :player_id,id from finding where target_id=:target_id')
            ->bindValue(':player_id', $model->player_id)
            ->bindValue(':target_id', $model->target_id)
            ->query();
          Yii::$app->db->createCommand('insert ignore into player_treasure (player_id,treasure_id) select :player_id,id from treasure where target_id=:target_id')
            ->bindValue(':player_id', $model->player_id)
            ->bindValue(':target_id', $model->target_id)
            ->query();

          return $this->redirect(['view', 'player_id' => $model->player_id, 'target_id' => $model->target_id]);
      }

      return $this->render('create', [
          'model' => $model,
      ]);
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
        $model = $this->findModel($player_id, $target_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
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
        if (($model = Headshot::findOne(['player_id' => $player_id, 'target_id' => $target_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
