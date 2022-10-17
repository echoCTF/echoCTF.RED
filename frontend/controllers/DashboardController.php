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
use app\models\News;
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
                'eventActive'=> [
                  'actions' => [''],
                ],
                'eventStartEnd'=>[
                  'actions' => [''],
                ],
                'eventEnd'=>[
                  'actions' => [''],
                ],
                'teamsAccess'=>[
                  'actions'=>['']
                ],
                'eventStart'=>[
                  'actions' => [''],
                ],
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
      $dashboardStats->claims=(int) PlayerTreasure::find()->where(['player.academic'=>Yii::$app->user->identity->academic])->joinWith(['player'])->count();
      $rows = (new \yii\db\Query())
->select(['date_format(ts,"%D") as dat', 'count(*) as cnt','sum(if(player_id in ('.Yii::$app->user->id.'),1,0)) as pcnt'])
        ->from('stream')
        ->where(['>=','ts', new \yii\db\Expression('now()-interval 10 day')])
        ->groupBy(new \yii\db\Expression('date(ts)'))
        ->orderBy(new \yii\db\Expression('date(ts)'))
        ->all();
      $dayActivity=null;
      foreach($rows as $row)
      {
        $dayActivity['labels'][]="'".$row['dat']."'";
        $dayActivity['overallSeries'][]=$row['cnt'];
        $dayActivity['playerSeries'][]=$row['pcnt'];
      }
      $query=News::find()->orderBy(['created_at'=>SORT_DESC])->limit(3);
      $newsProvider=new ActiveDataProvider([
          'query' => $query,
          'pagination'=>false,
          'sort'=> ['defaultOrder' => ['created_at' => SORT_DESC]],
      ]);

      return $this->render('index', [
          'totalPoints'=>0,
          'dashboardStats'=>$dashboardStats,
          'newsProvider'=>$newsProvider,
          'dayActivity'=>$dayActivity
      ]);
    }

}
