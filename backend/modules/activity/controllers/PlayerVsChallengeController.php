<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Challenge;
use app\modules\gameplay\models\Question;
use app\modules\activity\models\PlayerQuestion;
use app\modules\activity\models\ChallengeSolver;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\activity\models\PlayerVsChallengeForm;
use yii\helpers\ArrayHelper;

/**
 * PlayerbadgeController implements the CRUD actions for PlayerBadge model.
 */
class PlayerVsChallengeController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
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
