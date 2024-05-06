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
  public function run(int $id, $baseURL = "https://echoctf.red/activate/")
  {

    // Get innactive players
    $player = $this->controller->findModel($id);
    if ($player->status == 10)
    {
      \Yii::$app->getSession()->setFlash('warning', Yii::t('app', 'Player already active skipping mail.'));
    }
    elseif ($player->status == 9)
    {
      if (\Yii::$app->sys->player_require_approval === true && $player->approval > 0 && $player->approval <= 2)
        $this->approvalMail($player);
      elseif (\Yii::$app->sys->player_require_approval === true && $player->approval > 2 && $player->approval <= 4)
        $this->rejectionMail($player);
    }
    elseif ($player->status == 0)
    {
      $this->rejectionMail($player);
    }

    if ($player->approval == 1)
      $player->updateAttributes(['approval' => 2]);
    elseif ($player->approval == 3)
      $player->updateAttributes(['approval' => 4]);

    return $this->controller->goBack(Yii::$app->request->referrer);
  }

  /**
   * Send player rejection mail
   */
  private function rejectionMail($player)
  {
    try {
      $emailtpl=\app\modules\content\models\EmailTemplate::findOne(['name' => 'rejectVerify']);
      $contentHtml = $this->controller->renderPhpContent("?>" . $emailtpl->html, ['user' => $player]);
      $contentTxt = $this->controller->renderPhpContent("?>" . $emailtpl->txt, ['user' => $player]);
      $subject=Yii::t('app', '{event_name} Account rejected', ['event_name' => trim(Yii::$app->sys->event_name)]);
      if(!$player->mail($subject,$contentHtml,$contentTxt))
      {
        throw new \Exception('Could not send mail');
      }

      if (Yii::$app->sys->player_require_approval === true && $player->approval == 3) {
        $player->updateAttributes(['approval' => 4]);
        \Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Player rejection mail send and approval status updated.'));
      } else {
        \Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Player rejection mail send.'));
      }
    } catch (\Exception $e) {
      \Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Failed to mail rejection to player. {exception}', ['exception' => Html::encode($e->getMessage())]));
    }
  }

  /**
   * Send approval email to user
   */
  private function approvalMail($player)
  {
    try {
      $activationURL = sprintf("https://%s/verify-email?token=%s", \Yii::$app->sys->offense_domain, $player->verification_token);
      $emailtpl=\app\modules\content\models\EmailTemplate::findOne(['name' => 'emailVerify']);
      $contentHtml = $this->controller->renderPhpContent("?>" . $emailtpl->html, ['user' => $player,'verifyLink'=>$activationURL]);
      $contentTxt = $this->controller->renderPhpContent("?>" . $emailtpl->txt, ['user' => $player,'verifyLink'=>$activationURL]);
      $subject=Yii::t('app', '{event_name} Account approved', ['event_name' => trim(Yii::$app->sys->event_name)]);

      if(!$player->mail($subject,$contentHtml,$contentTxt))
      {
        throw new \Exception('Could not send mail');
      }

      if (Yii::$app->sys->player_require_approval === true && $player->approval == 1) {
        $player->updateAttributes(['approval' => 2]);
        \Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Player activation mail send and approval status updated.'));
      } else {
        \Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Player activation mail send.'));
      }
    } catch (\Exception $e) {
      \Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Failed to mail player. {exception}', ['exception' => Html::encode($e->getMessage())]));
    }
  }
}
