<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\modules\frontend\models\TeamPlayer;
use app\modules\frontend\models\TeamPlayerSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * TeamplayerController implements the CRUD actions for TeamPlayer model.
 */
class TeamplayerController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }

    /**
     * Lists all TeamPlayer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new TeamPlayerSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TeamPlayer model.
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
     * Creates a new TeamPlayer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new TeamPlayer();
        if(\app\modules\frontend\models\Player::find()->count() == 0)
        {
          // If there are no player redirect to create player page
          Yii::$app->session->setFlash('warning', Yii::t('app',"No Players found create one first."));
          return $this->redirect(['/frontend/player/create']);
        }
        if(\app\modules\frontend\models\Team::find()->count() == 0)
        {
          // If there are no player redirect to create player page
          Yii::$app->session->setFlash('warning', Yii::t('app',"No Teams found create one first."));
          return $this->redirect(['/frontend/team/create']);
        }

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TeamPlayer model.
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
     * Deletes an existing TeamPlayer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if(is_array(Yii::$app->getSession()->get('__deleteUrl')))
        {
          Yii::$app->getUser()->setReturnUrl(Yii::$app->getSession()->get('__deleteUrl'));
          unset($_SESSION['__deleteUrl']);
        }

        if($this->findModel($id)->delete()!==false)
          Yii::$app->session->setFlash('success', "Team membership deleted.");

        return $this->redirect(Yii::$app->request->referrer ?? ['frontend/teamplayer/index']);
    }

    /**
     * Toggles an existing TeamPlayer approval flag.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionToggleApproved($id)
    {
        $model=$this->findModel($id);
        $model->updateAttributes(['approved' => !$model->approved]);
        $t=Yii::$app->db->createCommand("CALL repopulate_team_stream(:tid)")->bindValue(':tid',$model->team_id)->execute();
        return $this->redirect(Yii::$app->request->referrer ?? ['frontend/teamplayer/index']);
    }

    /**
     * Finds the TeamPlayer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TeamPlayer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(($model=TeamPlayer::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app','The requested page does not exist.'));
    }
}
