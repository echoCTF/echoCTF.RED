<?php

namespace app\modules\content\controllers;

use Yii;
use app\modules\content\models\EmailTemplate;
use app\modules\content\models\EmailTemplateSearch;
use app\modules\frontend\models\Player;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * EmailTemplateController implements the CRUD actions for EmailTemplate model.
 */
class EmailTemplateController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), []);
  }

  /**
   * Lists all EmailTemplate models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new EmailTemplateSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single EmailTemplate model.
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
   * Creates a new EmailTemplate model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate()
  {
    $model = new EmailTemplate();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['view', 'id' => $model->id]);
    }

    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Ad hoc mailer.
   * Mass mail your players with an ad hoc message
   * @return mixed
   */
  public function actionAdhocMail()
  {
    $model = new EmailTemplate();

    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
      $successes = $failures = 0;
      foreach (Player::find()->where(['status' => 10])->orderBy(['id' => SORT_ASC])->all() as $p) {
        //public function mail($subject, $html, $txt, $headers = [])
        if ($p->mail($model->title, $model->html, $model->txt))
          $successes++;
        else {
          $failures--;
          $failed[] = $p->username . " " . $p->email;
        }
      }
      Yii::$app->session->setFlash('success', Yii::t('app', 'Mailed [<code><b>{successes}</b></code>] players.', ['successes' => intval($successes)]));
      if ($failures > 0)
        Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to mail [<code><b>{failures}</b></code>] players', ['failures' => intval($failures)]));
      return $this->redirect(['adhoc-mail']);
    }

    return $this->render('adhoc-mail', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing EmailTemplate model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['view', 'id' => $model->id]);
    }

    return $this->render('update', [
      'model' => $model,
    ]);
  }

  /**
   * Deletes an existing EmailTemplate model.
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
   * Finds the EmailTemplate model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return EmailTemplate the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = EmailTemplate::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
