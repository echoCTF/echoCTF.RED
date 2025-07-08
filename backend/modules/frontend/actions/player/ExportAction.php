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
    $statuses=[10 => 'Enabled', 9 => 'Inactive', 8 => "Change", 0 => "Deleted",];
        $csv = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');
        fputcsv($csv, ['Username', 'Full Name', 'Email', 'Category', 'Affiliation', 'Approval Stage',"Account status"]);
        foreach(Player::find()->all() as $p)
        {
          fputcsv($csv, [$p->username, $p->fullname, $p->email, $p->academicShort, $p->metadata->affiliation, $p::APPROVAL[$p->approval],$statuses[$p->status]]);
        }
        rewind($csv);
        return \Yii::$app->response->sendStreamAsFile($csv,'players-full.csv');
  }

}
