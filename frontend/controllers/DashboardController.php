<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\target\models\Target;
use app\modules\target\models\Treasure;
use app\modules\game\models\Headshot;
use app\models\PlayerTreasure;
use app\models\PlayerScore;
use app\models\Profile;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class DashboardController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
          'access' => [
              'class' => AccessControl::class,
              'rules' => [
                [
                    'allow' => true,
                    'roles'=>['@']
                ],
              ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                ],
            ],
        ]);
    }

    public function actionIndex()
    {
      $dashboardStats=new \stdClass();
      $dashboardStats->countries=(int) Profile::find()->select(['country'])->distinct()->count();
      $dashboardStats->claims=(int) PlayerTreasure::find()->count();

      return $this->render('index', [
          'totalPoints'=>0,
          'dashboardStats'=>$dashboardStats
      ]);
    }

}
