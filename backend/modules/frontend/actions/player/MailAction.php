<?php
namespace app\modules\frontend\actions\player;

use Yii;

use app\modules\frontend\models\Player;
use app\modules\frontend\models\Team;
use app\modules\frontend\models\TeamPlayer;
use app\modules\frontend\models\PlayerSsl;
use app\modules\frontend\models\PlayerSearch;
use app\modules\settings\models\Sysconfig;
use yii\helpers\Html;

class MailAction extends \yii\base\Action
{

  /*
    Mail Users their activation URL
  */
  public function run(int $id, $baseURL="https://echoctf.red/activate/")
  {

    // Get innactive players
    $player=$this->controller->findModel($id);
    if($player->status==10)
    {
      \Yii::$app->getSession()->setFlash('warning', 'Player already active skipping mail.');
      return $this->controller->goBack(Yii::$app->request->referrer);
    }
    elseif($player->status==9)
    {
      //$event_name=Sysconfig::findOne('event_name')->val;
      try {
        $activationURL=sprintf("https://%s/verify-email?token=%s",\Yii::$app->sys->offense_domain, $player->verification_token);
          Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $player,'verifyLink'=>$activationURL]
            )
            ->setFrom([Yii::$app->sys->mail_from => Yii::$app->sys->mail_fromName.' robot'])
            ->setTo([$player->email => $player->fullname])
            ->setSubject(trim(Yii::$app->sys->event_name). ' Account approved')
            ->send();
            \Yii::$app->getSession()->setFlash('success', 'Player activation mail send.');
        }
        catch(\Exception $e)
        {
          \Yii::$app->getSession()->setFlash('error', 'Failed to mail player. '.Html::encode($e->getMessage()));
        }
    }
    elseif($player->status==0)
    {

      try {
        $activationURL=sprintf("https://%s/verify-email?token=%s",\Yii::$app->sys->offense_domain, $player->verification_token);
          Yii::$app
            ->mailer
            ->compose(
                ['html' => 'rejectVerify-html', 'text' => 'rejectVerify-text'],
                ['user' => $player]
            )
            ->setFrom([Yii::$app->sys->mail_from => Yii::$app->sys->mail_fromName.' robot'])
            ->setTo([$player->email => $player->fullname])
            ->setSubject(trim(Yii::$app->sys->event_name). ' Account Rejected')
            ->send();
            \Yii::$app->getSession()->setFlash('success', 'Player rejection mail send.');
        }
        catch(\Exception $e)
        {
          \Yii::$app->getSession()->setFlash('error', 'Failed to mail rejection to player. '.Html::encode($e->getMessage()));
        }

    }

    // Generate activation URL
//    $activationURL=sprintf("%s%s", $baseURL, $player->activkey);
//    $content=$this->controller->renderPartial('_account_activation_email', ['player' => $player, 'activationURL'=>$activationURL, 'event_name'=>$event_name]);
//    $player->mail($content, 'echoCTF RED re-sending of account activation URL');


    return $this->controller->goBack(Yii::$app->request->referrer);
  }
}
