<?php
/**
 * Vote widget
 * sample usage:
 *
 *  use app\components\widgets\vote\VoteWidget;
 *  $fakedModel = (object)['title'=> 'A Product', ];
 *  echo VoteWidget::widget(['model' => $fakedModel]);
 */

namespace app\widgets\leaderboard;

use Yii;
use yii\base\Widget;
use yii\widgets\ListView;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use app\models\PlayerScore;
use app\models\PlayerRank;
use app\models\Profile;

class Leaderboard extends Widget
{
    public $divID='Leaderboard';
    public $dataProvider;
    public $player_id;
    public $totalPoints;
    public $pagerID='leaderboard-pager';
    public $pageSize=11;
    public $summary='<div class="card-header card-header-primary"><h4 class="card-title">{TITLE}</h4><p class="card-category">{CATEGORY}</p></div>';
    public $title="Scoreboard";
    public $category="List of players by points. <small>Updated every 10 minutes</small>";

    public function init()
    {
      if($this->player_id!==NULL)
      {

        $this->dataProvider = new ActiveDataProvider([
//          'query' => PlayerScore::find()->active()->orderBy(['points'=>SORT_DESC,'player_id'=>SORT_ASC]),
          'query' => PlayerRank::find()->orderBy(['id'=>SORT_ASC,'player_id'=>SORT_ASC]),
          'pagination' => [
              'pageSizeParam'=>'score-perpage',
              'pageParam'=>'score-page',
              'pageSize' => $this->pageSize,
          ]
        ]);
      } else {
        $this->player_id=Yii::$app->user->id;
      }

      $rank=Profile::find()->where(['player_id'=>$this->player_id])->one()->rank;
      if(Yii::$app->request->get('score-page')===null && $rank!=null) {
              $this->dataProvider->pagination->page = ($rank->id-1)/$this->dataProvider->pagination->pageSize;
      }

      if($this->totalPoints===null)
      {
        $command = Yii::$app->db->createCommand('SELECT (SELECT IFNULL(SUM(points),0) FROM finding)+(SELECT IFNULL(SUM(points),0) FROM treasure)+(SELECT IFNULL(SUM(points),0) FROM badge)+(SELECT IFNULL(SUM(points),0) FROM question WHERE player_type=:player_type)');
        $command->bindValue(':player_type','offense');
        $this->totalPoints = $command->queryScalar();
      }
      $this->summary=\Yii::t('app', $this->summary, ['TITLE' => $this->title, 'CATEGORY'=>$this->category]);

        parent::init();
    }

    public function run()
    {
        LeaderboardAsset::register($this->getView());
        return $this->render('leaderboard',['dataProvider'=>$this->dataProvider,'totalPoints'=>$this->totalPoints,'divID'=>$this->divID,'pagerID'=>$this->pagerID,'player_id'=>$this->player_id,'summary'=>$this->summary]);
    }
}
