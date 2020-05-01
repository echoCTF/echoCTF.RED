<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\target\models\Target;
use app\modules\target\models\Treasure;
use app\modules\game\models\Headshot;
use app\models\PlayerTreasure;
use app\models\PlayerScore;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class DashboardController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                ],
            ],
        ];
    }

    public function actionIndex()
    {

      $command=Yii::$app->db->createCommand('SELECT (SELECT IFNULL(SUM(points),0) FROM finding)+(SELECT IFNULL(SUM(points),0) FROM treasure)+(SELECT IFNULL(SUM(points),0) FROM badge)+(SELECT IFNULL(SUM(points),0) FROM question WHERE player_type=:player_type)');
      $command->bindValue(':player_type', 'offense');
      $totalPoints=$command->queryScalar();
      $treasureStats=new \stdClass();
      $treasureStats->total=(int) Treasure::find()->count();
      $treasureStats->claims=(int) PlayerTreasure::find()->count();
      $treasureStats->claimed=(int) PlayerTreasure::find()->where(['player_id'=>Yii::$app->user->id])->count();
      $totalHeadshots=Headshot::find()->count();

      return $this->render('index', [
          'totalPoints'=>$totalPoints,
          'treasureStats'=>$treasureStats,
          'totalHeadshots'=>$totalHeadshots,
      ]);
    }

}
