<?php

namespace app\modules\game\controllers;

use Yii;
use yii\web\Controller;
use app\overloads\yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use app\modules\game\models\Headshot;

/**
 * Badge controller for the `game` module
 */
class BadgeController extends \app\components\BaseController
{
  public $layout = '//badge';
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), [
      'access' => [
        'class' => AccessControl::class,
        'rules' => [
          'badgeAllow' => [
            'actions' => ['headshot',],
            'allow' => true,
          ],
        ],
      ],
    ]);
  }

  public function actionHeadshot($profile_id, $target_id)
  {
    $profile = \app\models\Profile::findOne($profile_id);
    if ($profile === null || !$profile->visible) {
      Yii::$app->session->setFlash('warning', \Yii::t('app', 'No such profile found!'));
      return $this->redirect(['/']);
    }
    $headshot = Headshot::findOne(['player_id' => $profile->player_id, 'target_id' => $target_id]);
    $topH = Headshot::find()->select('player_id')->where(['target_id' => $target_id])->orderBy(['timer' => SORT_ASC])->limit(10)->asArray()->all();
    if ($profile == null || $headshot == null)
      return $this->redirect(['/']);

    return $this->render('index', [
      'headshot' => $headshot,
      'top' => ArrayHelper::getColumn($topH, 'player_id'),
    ]);
  }
}
