<?php

namespace app\modules\frontend\actions\player;

use Yii;

use app\modules\frontend\models\Player;
use app\modules\frontend\models\Team;
use app\modules\frontend\models\TeamPlayer;
use app\modules\frontend\models\PlayerSsl;
use app\modules\frontend\models\PlayerSearch;
use app\modules\settings\models\Sysconfig;

class ExportAction extends \yii\base\Action
{
  /**
   * Export Full Player Details
   *
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function run()
  {
    $csv = fopen('php://temp/maxmemory:' . (5 * 1024 * 1024), 'r+');
    $grouping = intval(Yii::$app->sys->academic_grouping) > 0;
    $identification = Yii::$app->sys->player_require_identification === true;
    $approval = Yii::$app->sys->player_require_approval !== false;
    $header = ['Username', 'Full Name', 'Email'];
    if ($grouping) $header[] = 'Category';
    if ($identification) $header[] = 'Affiliation';
    if ($approval) $header[] = 'Approval Stage';
    $header[] = 'Account status';
    fputcsv($csv, $header);
    foreach (Player::find()->with('metadata')->all() as $p) {
      $row = [$p->username, $p->fullname, $p->email];
      if ($grouping) $row[] = $p->academicShort;
      if ($identification) $row[] = ($p->metadata ? $p->metadata->affiliation : '');
      if ($approval) $row[] = $p::APPROVAL[$p->approval];
      $row[] = Player::STATUSES[$p->status];
      fputcsv($csv, $row);
    }
    rewind($csv);
    return \Yii::$app->response->sendStreamAsFile($csv, 'players-full.csv');
  }
}
