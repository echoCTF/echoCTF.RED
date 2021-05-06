<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Challenge;
use app\modules\gameplay\models\Question;
use app\modules\activity\models\PlayerQuestion;
use app\modules\activity\models\ChallengeSolver;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\activity\models\PlayerVsChallengeForm;

/**
 * PlayerbadgeController implements the CRUD actions for PlayerBadge model.
 */
class PlayerVsChallengeController extends Controller
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
      $model = new PlayerVsChallengeForm();

      if ($model->load(Yii::$app->request->post()) && $model->validate()) {
          return $this->render('progress', ['model' => $model]);
      } else {
          // either the page is initially displayed or there is some validation error
          return $this->render('index', ['model' => $model]);
      }
    }
}
