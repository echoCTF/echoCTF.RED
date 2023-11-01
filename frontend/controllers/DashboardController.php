<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use app\modules\target\models\Target;
use app\modules\target\models\Treasure;
use app\modules\game\models\Headshot;
use app\models\PlayerTreasure;
use app\models\PlayerScore;
use app\models\Profile;
use app\models\News;
use yii\helpers\ArrayHelper;
use app\overloads\yii\filters\AccessControl;
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
      $active_targets=intval(\app\modules\target\models\Target::find()->active()->count());
      $active_challenges=intval(\app\modules\challenge\models\Challenge::find()->alias('t')->active()->count());
      $dashboardStats->countries=(int) Profile::find()->select(['country'])->distinct()->count();
      if(Yii::$app->sys->academic_grouping!==false)
        $dashboardStats->claims=(int) PlayerTreasure::find()->where(['player.academic'=>Yii::$app->user->identity->academic])->joinWith(['player'])->count();
      else
        $dashboardStats->claims=(int) PlayerTreasure::find()->count();
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
      $visits=Yii::$app->session->get('last_targets_visited');
      if($visits!==null && count($visits)>0)
      {
        $visitsSTR=implode(',',$visits);
        $last_targets_visited=Target::find()->addSelect("id,name")
              ->active()
              ->andWhere(['IN','id',$visits])
              ->orderBy([new \yii\db\Expression("FIELD (id, $visitsSTR)")])
              ->asArray()->all();
      }
      else
        $last_targets_visited=[];
      $lastVisitsProvider = new ArrayDataProvider([
        'allModels' => $last_targets_visited,
        'sort' => [],
        'pagination' => [
          'pageSize' => 5,
       ],
      ]);
      $query=News::find()->orderBy(['created_at'=>SORT_DESC])->limit(4);
      $newsProvider=new ActiveDataProvider([
          'query' => $query,
          'pagination'=>false,
          'sort'=> ['defaultOrder' => ['created_at' => SORT_DESC]],
      ]);

      return $this->render('index', [
          'totalPoints'=>0,
          'active_targets'=>$active_targets,
          'active_challenges'=>$active_challenges,
          'lastVisitsProvider'=>$lastVisitsProvider,
          'dashboardStats'=>$dashboardStats,
          'newsProvider'=>$newsProvider,
          'dayActivity'=>$dayActivity
      ]);
    }

}
