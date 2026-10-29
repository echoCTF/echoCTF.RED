<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\activity\models\PlayerTargetTrafficSearch;
use app\components\BaseController;
use yii\filters\VerbFilter;

/**
 * PlayerTargetTrafficController lists per player, per target traffic counters.
 */
class PlayerTargetTrafficController extends BaseController
{
  public function behaviors()
  {
    return array_merge(
      parent::behaviors(),
      [
        'verbs' => [
          'class' => VerbFilter::class,
          'actions' => [
            'truncate' => ['POST'],
            'delete-filtered' => ['POST'],
          ],
        ],
      ]
    );
  }

  /**
   * Lists player/target traffic counters.
   * @return string
   */
  public function actionIndex()
  {
    $searchModel = new PlayerTargetTrafficSearch();
    $dataProvider = $searchModel->search($this->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Deletes the records matching the current grid filter.
   * @return \yii\web\Response
   */
  public function actionDeleteFiltered()
  {
    $searchModel = new PlayerTargetTrafficSearch();
    $query = $searchModel->search(['PlayerTargetTrafficSearch' => Yii::$app->request->post()]);
    $query->pagination = false;

    $trans = Yii::$app->db->beginTransaction();
    try {
      $counter = $query->count;
      foreach ($query->getModels() as $q)
        $q->delete();
      $trans->commit();
      Yii::$app->session->setFlash('success', Yii::t('app', '[<code><b>{counter}</b></code>] Player Target Traffic records deleted', ['counter' => intval($counter)]));
    } catch (\Throwable $e) {
      $trans->rollBack();
      Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to delete player target traffic records'));
    }
    return $this->redirect(['index']);
  }

  /**
   * Truncates the player_target_traffic table.
   * @return \yii\web\Response
   */
  public function actionTruncate()
  {
    try {
      Yii::$app->db->createCommand()->truncateTable('player_target_traffic')->execute();
      Yii::$app->session->setFlash('success', Yii::t('app', 'Player Target Traffic truncated'));
    } catch (\Throwable $e) {
      Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to truncate Player Target Traffic'));
    }
    return $this->redirect(['index']);
  }
}
