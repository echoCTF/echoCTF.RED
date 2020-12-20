<?php
namespace app\modules\frontend\actions\player;

use Yii;

use app\modules\frontend\models\Player;
use app\modules\frontend\models\Team;
use app\modules\frontend\models\TeamPlayer;
use app\modules\frontend\models\PlayerSsl;
use app\modules\frontend\models\PlayerSearch;
use app\modules\settings\models\Sysconfig;

class MailAction extends \yii\base\Action
{

  /*
    Mail Users their activation URL
  */
  public function run(int $id, $baseURL="https://echoctf.red/activate/")
  {
    // Get innactive players
    $player=$this->controller->findModel($id);
    $event_name=Sysconfig::findOne('event_name')->val;
    // Generate activation URL
    $activationURL=sprintf("%s%s", $baseURL, $player->activkey);
    $content=$this->controller->renderPartial('_account_activation_email', ['player' => $player, 'activationURL'=>$activationURL, 'event_name'=>$event_name]);
    $player->mail($content, 'echoCTF RED re-sending of account activation URL');
    return $this->controller->goBack(Yii::$app->request->referrer);
  }
}
