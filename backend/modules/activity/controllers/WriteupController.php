<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\activity\models\Writeup;
use app\modules\activity\models\WriteupSearch;
use yii\base\UserException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\modules\settings\models\Language;
use app\modules\gameplay\models\Target;

/**
 * WriteupController implements the CRUD actions for Writeup model.
 */
class WriteupController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), []);
  }

  /**
   * Lists all Writeup models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new WriteupSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $langIds = Writeup::find()
        ->select('language_id')
        ->distinct()
        ->column();

    $targetIds = Writeup::find()
        ->select('target_id')
        ->distinct()
        ->column();

    $languages=ArrayHelper::map(
          Language::find()
              ->where(['in', 'id', $langIds])
              ->all(),
          'id',
          'l'
    );
    $targets=ArrayHelper::map(
          Target::find()
              ->where(['in', 'id', $targetIds])
              ->orderBy('name')
              ->all(),
          'id',
          'name'
    );

    return $this->render('index', [
      'languages'=>$languages,
      'targets'=>$targets,
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single Writeup model.
   * @param integer $player_id
   * @param integer $target_id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionView($player_id, $target_id)
  {
    $model=$this->findModel($player_id, $target_id);
    $targetWriteups=Writeup::find()->where([
      'and',
      ['!=','player_id',$model->player_id],
      ['target_id'=>$model->target_id]
    ])->count();
    $playerWriteups=Writeup::find()->where([
      'and',
      ['player_id'=>$model->player_id],
      ['!=','target_id',$model->target_id]
    ])->count();
    return $this->render('view', [
      'playerWriteups'=>intval($playerWriteups),
      'targetWriteups'=>intval($targetWriteups),
      'model' => $model,
    ]);
  }

  /**
   * Creates a new Writeup model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate()
  {
    $model = new Writeup();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['view', 'player_id' => $model->player_id, 'target_id' => $model->target_id]);
    }

    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing Writeup model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $player_id
   * @param integer $target_id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate(int $player_id, int $target_id)
  {
    $model = $this->findModel($player_id, $target_id);
    $oldmodel = $model->attributes;
    $model->cleanup();
    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      if ($oldmodel['status'] !== $model->status) {
        $t=\Yii::t('app', "The status of the writeup for [{target_name}] by [{username}], has changed to [{status}].", ['target_name' => $model->target->name, 'username' => $model->player->username, 'status' => $model->status]);
        $model->player->notify('info',$t,$t);
      }
      return $this->redirect(['view', 'player_id' => $model->player_id, 'target_id' => $model->target_id]);
    }

    return $this->render('update', [
      'model' => $model,
    ]);
  }

  /**
   * Deletes an existing Writeup model.
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
   * Approves an existing Writeup model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param integer $player_id
   * @param integer $target_id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionApprove($player_id, $target_id)
  {
    $transaction = Yii::$app->db->beginTransaction();
    try {
      $model = $this->findModel($player_id, $target_id);
      $model->approved = true;
      $model->status = 'OK';
      $model->comment = null;
      foreach ($model->target->treasures as $treasure) {
        $string = mb_ereg_replace($treasure->code, '*REDUCTED*', $model->content);
        $model->content = $string;
      }
      if ($model->save()) {
        $t=Yii::t('app', "The writeup you submitted for {target_name} has been approved. Thank you!", ['target_name' => $model->target->name]);
        $model->player->notify('info',$t,$t);
        Yii::$app->session->setFlash('success', Yii::t('app', 'Writeup for {target_name} by {username} approved.', ['target_name' => $model->target->name, 'username' => $model->player->username]));
      } else {
        Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to approve writeup for {target_name} by {username}.', ['target_name' => $model->target->name, 'username' => $model->player->username]));
      }
      $transaction->commit();
    } catch (\Exception $e) {
      $transaction->rollBack();
      Yii::$app->session->setFlash('error', Html::encode($e->getMessage()));
    }
    return $this->redirect(['index']);
  }



  /**
   * Finds the Writeup model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $player_id
   * @param integer $target_id
   * @return Writeup the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($player_id, $target_id)
  {
    if (($model = Writeup::findOne(['player_id' => $player_id, 'target_id' => $target_id])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app','The requested page does not exist.'));
  }
}
