<?php

/**
 * Challenge Solver widget
 */

namespace app\widgets\solver;

use yii\base\Widget;
use yii\helpers\Html;
use app\components\ProfileVisibility as PV;

class SolverWidget extends Widget
{
  public $challenge_id;
  public $slice = 19;
  public $htmlOptions = ['class' => 'card bg-dark solves'];

  public function init()
  {
    parent::init();
  }

  public function run()
  {
    // Register AssetBundle
    SolverWidgetAsset::register($this->getView());
    $solvers = [];
    $sql = "SELECT player.id,player.username,profile.id as profile_id,profile.visibility FROM challenge_solver LEFT JOIN player ON player.id=challenge_solver.player_id LEFT JOIN profile on profile.player_id=player.id WHERE challenge_solver.challenge_id=:challenge_id AND player.status=10 ORDER BY challenge_solver.created_at DESC,challenge_solver.player_id asc LIMIT 50";
    $result = \Yii::$app->db->createCommand($sql, [':challenge_id' => $this->challenge_id])->cache(180)->query();

    foreach ($result as $hs) {
      $to = Html::encode($hs['username']);

      if (intval(\Yii::$app->user->id) === intval($hs['id']))
        $to = Html::a(Html::encode($hs['username']), ['/profile/me'], ['data-pjax' => 0]);
      else if (PV::visible($hs['id'], $hs['visibility']) === true)
        $to = Html::a(Html::encode($hs['username']), ['/profile/index', 'id' => $hs['profile_id']], ['data-pjax' => 0]);

      $solvers[] = $to;
    }

    return $this->render('_card', ['solvers' => $solvers, 'htmlOptions' => $this->htmlOptions, 'slice' => $this->slice]);
  }
}
