<?php

namespace app\modules\administer\controllers;

use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use Yii;
use app\modules\administer\models\EventModel;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;

class EventsController extends \app\components\BaseController
{
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), [
      'access' => [
        'class' => \yii\filters\AccessControl::class,
        'rules' => [
          'authActions' => [
            'allow' => \Yii::$app->user->identity && \Yii::$app->user->identity->isAdmin,
            'actions' => ['index', 'view'],
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
    ]);
  }

  public function actionIndex()
  {
    $dataProvider = new \yii\data\ArrayDataProvider([
      'allModels' => EventModel::getAll(),
      'pagination' => ['pageSize' => 10],
    ]);

    return $this->render('index', ['dataProvider' => $dataProvider]);
  }

  public function actionView($name)
  {
    $model = $this->findModel($name);

    // Get actual event code
    $eventCode = Yii::$app->db
      ->createCommand("SHOW CREATE EVENT `{$model->Name}`")
      ->queryOne();

    return $this->render('view', [
      'model' => $model,
      'eventCode' => $eventCode['Create Event'] ?? 'N/A',
    ]);
  }

  public function actionCreate()
  {
    $model = new EventModel();

    if ($model->load(Yii::$app->request->post())) {
      $newSql = str_replace(["\r\n", "\r"], "\n", $model->Event_comment);

      try {
        Yii::$app->db->createCommand($newSql)->execute();

        if (preg_match('/EVENT\s+`([^`]+)`/i', $newSql, $matches)) {
          $eventName = $matches[1];
          Yii::$app->session->setFlash('success', "Event created successfully.");
          return $this->redirect(['view', 'name' => $eventName]);
        } else {
          Yii::$app->session->setFlash('success', "Event created successfully.");
          return $this->redirect(['index']);
        }
      } catch (\Exception $e) {
        Yii::$app->session->setFlash('error', "Failed to create event: " . $e->getMessage());
      }
    }

    return $this->render('create', [
      'model' => $model,
    ]);
  }

  public function actionUpdate($name)
  {
    $model = $this->findModel($name);

    // Get current CREATE EVENT SQL
    $oldSql = Yii::$app->db
      ->createCommand("SHOW CREATE EVENT `{$model->Name}`")
      ->queryOne()['Create Event'];

    if ($model->load(Yii::$app->request->post())) {
      $newSql = str_replace(["\r\n", "\r"], "\n", $model->Event_comment);

      try {
        // Try to drop old and create new event
        Yii::$app->db->createCommand("DROP EVENT IF EXISTS `{$model->Name}`")->execute();
        Yii::$app->db->createCommand($newSql)->execute();

        Yii::$app->session->setFlash('success', "Event updated successfully.");
        return $this->redirect(['view', 'name' => $model->Name]);
      } catch (\Exception $e) {
        // If failed, recreate the old event
        try {
          Yii::$app->db->createCommand($oldSql)->execute();
        } catch (\Exception $rollback) {
          Yii::$app->session->setFlash('error', "Failed to update event and rollback also failed: " . $rollback->getMessage());
          return $this->redirect(['view', 'name' => $model->Name]);
        }

        Yii::$app->session->setFlash('error', "Failed to update event, rolled back to previous version: " . $e->getMessage());
        return $this->redirect(['view', 'name' => $model->Name]);
      }
    }

    // Pass current SQL to the form
    $model->Event_comment = $oldSql;

    return $this->render('update', [
      'model' => $model,
    ]);
  }

  public function actionDelete($name)
  {
    EventModel::dropEvent($name);
    return $this->redirect(['index']);
  }

  protected function findModel($name)
  {
    $events = EventModel::getAll();
    foreach ($events as $event) {
      if ($event->Name === $name) {
        return $event;
      }
    }
    throw new NotFoundHttpException("Event not found: $name");
  }
}
