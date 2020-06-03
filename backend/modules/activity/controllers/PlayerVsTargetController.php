<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;
use app\modules\activity\models\PlayerFinding;
use app\modules\activity\models\PlayerTreasure;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\activity\models\PlayerVsTargetForm;

/**
 * PlayerbadgeController implements the CRUD actions for PlayerBadge model.
 */
class PlayerVsTargetController extends Controller
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
          ];
      }

    public function actionIndex()
    {
      $model = new PlayerVsTargetForm();

      if ($model->load(Yii::$app->request->post()) && $model->validate()) {
          return $this->render('progress', ['model' => $model]);
      } else {
          // either the page is initially displayed or there is some validation error
          return $this->render('index', ['model' => $model]);
      }
    }
}
