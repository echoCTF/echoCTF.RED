<?php

/**
 * Target Headshots widget
 */

namespace app\widgets\target;

use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\ProfileVisibility as PV;

class HeadshotsWidget extends Widget
{
  public $target_id;
  public function init()
  {
    parent::init();
  }

  public function run()
  {
    $headshots = [];
    $academicStr="";
    if(\Yii::$app->sys->academic_grouping!==false)
      $academicStr=" AND player.academic=".\Yii::$app->user->identity->academic;
    $sql = "SELECT player.id,player.username,profile.id as profile_id,profile.visibility FROM headshot LEFT JOIN player ON player.id=headshot.player_id LEFT JOIN profile on profile.player_id=player.id WHERE headshot.target_id=:target_id AND player.status=10 {$academicStr} ORDER BY headshot.created_at DESC,headshot.player_id asc LIMIT 50";
    $result = \Yii::$app->db->createCommand($sql, [':target_id' => $this->target_id])->cache(180)->query();

    foreach ($result as $hs) {
      $to = Html::encode($hs['username']);

      if (intval(\Yii::$app->user->id) === intval($hs['id']))
        $to = Html::a(Html::encode($hs['username']), ['/profile/me'], ['data-pjax' => 0]);
      else if (PV::visible($hs['id'], $hs['visibility']) === true)
        $to = Html::a(Html::encode($hs['username']), ['/profile/index', 'id' => $hs['profile_id']], ['data-pjax' => 0]);

      $headshots[] = $to;
    }

    if (!empty($headshots)) {
      echo "<code>", implode(", ", array_slice($headshots, 0, 19)), "</code>";
      if (count($headshots) > 19) {
        echo "<details class=\"headshotters\">";
        echo "<summary data-open=\"Hide more\" data-close=\"Show more\"></summary>";
        echo "<code>", implode(", ", array_slice($headshots, 19)), "</code>";
        echo "</details>";
      }
    } else {
      echo '<code>' . \Yii::t('app', 'no one yet...') . '</code>';
    }
  }
}
