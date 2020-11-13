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
                       'allow' => false,
                       'matchCallback' => function ($rule, $action) {
                         return Yii::$app->sys->event_start!==false && (time()<Yii::$app->sys->event_start || time()>Yii::$app->sys->event_end);
                       },
                       'denyCallback' => function() {
                         Yii::$app->session->setFlash('info', 'This area is disabled until the competition starts');
                         return  \Yii::$app->getResponse()->redirect(['/profile/me']);
                       }

                   ],
                   [
                      'actions' => ['index'],
                      'allow' => false,
                      'matchCallback' => function ($rule, $action) {
                        if(Yii::$app->sys->team_required===false)
                        {
                           return false;
                        }

                        if(Yii::$app->user->identity->teamPlayer===NULL)
                        {
                          Yii::$app->session->setFlash('warning', 'You need to join a team before being able to access this area.');
                          return true;
                        }
                        if(Yii::$app->user->identity->teamPlayer->approved!==1)
                        {
                          Yii::$app->session->setFlash('warning', 'You need to have your team membership approved before being able to access this area.');
                          return true;
                        }
                        return false;
                      },
                      'denyCallback' => function() {
                        return  \Yii::$app->getResponse()->redirect(['/team/default/index']);
                      }
                   ],
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
