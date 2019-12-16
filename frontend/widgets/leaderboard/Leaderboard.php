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
use app\models\Profile;

class Leaderboard extends Widget
{
    public $divID='Leaderboard';
    public $dataProvider;
    public $player_id;
    public $totalPoints;
    public $pagerID='leaderboard-pager';

    public function init()
    {
      if($this->player_id!==NULL)
      {
        $this->dataProvider = new ActiveDataProvider([
          'query' => PlayerScore::find()->active()->orderBy(['points'=>SORT_DESC,'player_id'=>SORT_ASC]),
          'pagination' => [
              'pageSizeParam'=>'score-perpage',
              'pageParam'=>'score-page',
              'pageSize' => 11,
          ]

        ]);
        if(Yii::$app->request->get('score-page')===null)
          $this->dataProvider->pagination->page = intval(Profile::find()->where(['player_id'=>$this->player_id])->one()->rank->id/11);

      }
      else {
        $this->player_id=Yii::$app->user->id;
      }

      if($this->totalPoints===null)
      {
        $command = Yii::$app->db->createCommand('SELECT (SELECT IFNULL(SUM(points),0) FROM finding)+(SELECT IFNULL(SUM(points),0) FROM treasure)+(SELECT IFNULL(SUM(points),0) FROM badge)+(SELECT IFNULL(SUM(points),0) FROM question WHERE player_type=:player_type)');
        $command->bindValue(':player_type','offense');
        $this->totalPoints = $command->queryScalar();
      }
        parent::init();
    }

    public function run()
    {
        LeaderboardAsset::register($this->getView());
        return $this->render('leaderboard',['dataProvider'=>$this->dataProvider,'totalPoints'=>$this->totalPoints,'divID'=>$this->divID,'pagerID'=>$this->pagerID,'player_id'=>$this->player_id]);
    }
}
