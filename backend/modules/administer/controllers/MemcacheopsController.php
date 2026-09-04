<?php

namespace app\modules\administer\controllers;

use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use Yii;
use app\modules\administer\models\EventModel;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;

class MemcacheopsController extends \app\components\BaseController
{
  protected $blacklistedKeys = [
    'sysconfig:event_name',
  ];

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

    return $this->render('index');
  }

  public function actionFetch($name)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    if (!(Yii::$app->cache instanceof \yii\caching\MemCache)) {
      return ['success' => false, 'error' => 'Memcache not initialized.'];
    }

    $value = Yii::$app->cache->memcache->get($name);
    if ($value === false) {
      return ['success' => false, 'error' => 'Key not found or value is false.'];
    }

    // For display, we'll convert arrays/objects to a readable format
    if (is_array($value) || is_object($value)) {
      $value = var_export($value, true);
    }

    return ['success' => true, 'value' => $value];
  }

  public function actionDelete()
  {
    $name = Yii::$app->request->post('name');
    if (!(\Yii::$app->cache instanceof \yii\caching\MemCache))
      throw new \LogicException('Memcache not initialized.');

    if (in_array($name, $this->blacklistedKeys)) {
      Yii::$app->session->setFlash('error', 'Cannot delete key "' . $name . '": This key is protected.');
      return $this->redirect(['index']);
    }


    if (Yii::$app->cache->memcache->delete($name)) {
      Yii::$app->session->setFlash('success', 'Cache entry deleted successfully.');
    } else {
      Yii::$app->session->setFlash('error', 'Failed to delete cache entry.');
    }
    return $this->redirect(['index']);
  }

  public function actionSave()
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
      return ['success' => false, 'error' => 'Invalid request.'];
    }

    if (!(Yii::$app->cache instanceof \yii\caching\MemCache)) {
      return ['success' => false, 'error' => 'Memcache not initialized.'];
    }

    $name = Yii::$app->request->post('name');
    $value = Yii::$app->request->post('value');
    $ttl = Yii::$app->request->post('ttl', 3600); // optional TTL, default 1 hour

    if (empty($name)) {
      return ['success' => false, 'error' => 'Key name cannot be empty.'];
    }

    // Optionally, you can decode JSON if value is structured
    // For simplicity we store as string; you may want to cast to appropriate type
    $stored = Yii::$app->cache->set($name, $value, $ttl);

    if ($stored) {
      return ['success' => true, 'message' => 'Cache entry saved successfully.'];
    } else {
      return ['success' => false, 'error' => 'Failed to save cache entry.'];
    }
  }
}
