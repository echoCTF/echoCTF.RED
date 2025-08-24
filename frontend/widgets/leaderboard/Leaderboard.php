<?php

/**
 * Leaderboard widget
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
  public $divID = 'Leaderboard';
  public $country;
  public $dataProvider;
  public $player_id;
  public $totalPoints;
  public $pagerID = 'leaderboard-pager';
  public $pageSize = 11;
  public $summary = '<div class="card-header card-header-primary"><h4 class="card-title">{TITLE}</h4><p class="card-category">{CATEGORY}</p></div>';
  public $title = "Scoreboard";
  public $category = "List of players by points. <small>Updated every 10 minutes</small>";
  public $pagination = true;
  public $layout = '{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer">{pager}</div>';
  private function PlayerLeaderboards()
  {
    if ($this->player_id !== null) {
      $academic = \app\models\Player::findOne($this->player_id)->academic;
      $this->dataProvider = new ActiveDataProvider([
        'query' => PlayerRank::find()->academic($academic)->orderBy(['id' => SORT_ASC, 'player_id' => SORT_ASC]),
        'pagination' => [
          'pageSizeParam' => $this->pagination ? 'score-perpage' : false,
          'forcePageParam' => $this->pagination,
          'pageParam' => $this->pagination ? 'score-page' : false,
          'pageSize' => $this->pageSize,
        ]
      ]);
    } else {
      $this->player_id = Yii::$app->user->id;
    }

    $rank = Profile::find()->where(['player_id' => $this->player_id])->one()->rank;
    if (!$this->pagination || (Yii::$app->request->get('score-page') === null && $rank != null))
      $this->dataProvider->pagination->page = ($rank->id - 1) / $this->dataProvider->pagination->pageSize;
  }
  private function countryLeaderboards()
  {
    if ($this->player_id !== null) {
      $this->dataProvider = new ActiveDataProvider([
        'query' => \app\models\PlayerCountryRank::find()->joinWith(['player'])->where(['country' => $this->country, 'player.status' => \app\models\Player::STATUS_ACTIVE])->orderBy(['id' => SORT_ASC, 'player_id' => SORT_ASC]),
        'pagination' => [
          'pageSizeParam' => $this->pagination ? 'country-score-perpage' : false,
          'pageParam' => $this->pagination ? 'country-score-page' : false,
          'pageSize' => $this->pageSize,
        ]
      ]);
    } else {
      $this->player_id = Yii::$app->user->id;
    }

    $rank = Profile::find()->where(['player_id' => $this->player_id])->one()->countryRank;
    if (!$this->pagination || (Yii::$app->request->get('country-score-page') === null && $rank != null))
      $this->dataProvider->pagination->page = ($rank->id - 1) / $this->dataProvider->pagination->pageSize;
  }

  public function init()
  {
    if (!$this->pagination)
      $this->layout = '{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer"></div>';;

    if ($this->totalPoints === null) {
      $command = Yii::$app->db->createCommand('SELECT (SELECT IFNULL(SUM(points),0) FROM finding)+(SELECT IFNULL(SUM(points),0) FROM treasure)+(SELECT IFNULL(SUM(points),0) FROM badge)+(SELECT IFNULL(SUM(points),0) FROM question WHERE player_type=:player_type)');
      $command->bindValue(':player_type', 'offense');
      $this->totalPoints = $command->queryScalar();
    }


    if ($this->country === null) {
      $this->PlayerLeaderboards();
    } else {
      $this->countryLeaderboards();
    }
    $this->summary = \Yii::t('app', $this->summary, ['TITLE' => $this->title, 'CATEGORY' => $this->category]);

    parent::init();
  }

  public function run()
  {
    LeaderboardAsset::register($this->getView());
    return $this->render('leaderboard', [
      'dataProvider' => $this->dataProvider,
      'totalPoints' => $this->totalPoints,
      'divID' => $this->divID,
      'pagerID' => $this->pagerID,
      'player_id' => $this->player_id,
      'summary' => $this->summary,
      'pagination' => $this->pagination,
      'layout' => $this->layout
    ]);
  }
}
