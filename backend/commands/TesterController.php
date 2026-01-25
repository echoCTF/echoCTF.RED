<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mailer\Transport\TransportFactory;
use Symfony\Component\Mailer\Transport\DebugTransport;
use Symfony\Component\Mime\Email;
use yii\console\widgets\Table;

/**
 * Tester console controller for system checks.
 */
class TesterController extends Controller
{

  public function actionIndex()
  {
    $this->stdout("*** TESTER COMMAND ***\n");

    echo Table::widget([
      'headers' => ['Action', 'Usage', 'Description'],
      'rows' => [
        ['Action' => 'tester/mail',      'Usage' => 'tester/mail email@example.com', 'Description' => 'Send a test mail with the current settings'],
        ['Action' => 'tester/ws-notify', 'Usage' => 'tester/ws-notify player', 'Description' => 'Send a test websocket notification to the given player by id'],
      ],
    ]);
  }

  /**
   * Test the mailer configuration by sending a test email.
   *
   * Usage:
   *   backend tester/mail test@example.com
   *
   * @param string $to Recipient email
   */
  public function actionMail($to)
  {
    $mailer = Yii::$app->mailer;
    try {

      $this->stdout("*** SETTINGS *** \n");
      if (\Yii::$app->sys->mail_useFileTransport) {
        $this->stdout("mail_useFileTransport: Yes\n");
        $this->stdout("mails folder: " . @\Yii::getAlias('@app/runtime/mail/') . "\n");
      }
      if (\Yii::$app->sys->dsn) $this->stdout("dsn: " . \Yii::$app->sys->dsn . "\n");
      if (\Yii::$app->sys->mail_from) $this->stdout("mail_from: " . \Yii::$app->sys->mail_from . "\n");
      if (\Yii::$app->sys->mail_fromName) $this->stdout("mail_fromName: " . \Yii::$app->sys->mail_fromName . "\n");
      if (\Yii::$app->sys->mail_host) $this->stdout("mail_host: " . \Yii::$app->sys->mail_host . "\n");
      if (\Yii::$app->sys->mail_port) $this->stdout("mail_port: " . \Yii::$app->sys->mail_port . "\n");
      if (\Yii::$app->sys->mail_username) $this->stdout("mail_username: " . \Yii::$app->sys->mail_username . "\n");
      if (\Yii::$app->sys->mail_password)  $this->stdout("mail_password: **USED BUT HIDDEN**\n");

      $result = $mailer->compose()
        ->setFrom([\Yii::$app->sys->mail_from => \Yii::$app->sys->mail_fromName])
        ->setTo($to)
        ->setSubject('echoCTF Installation Mail Test')
        ->setTextBody("This is a test email sent at " . date('Y-m-d H:i:s'))
        ->send();
      if ($result) {
        $this->stdout("✅ Test email successfully sent to {$to}\n");
      } else {
        $this->stderr("❌ Failed to send test email to {$to}\n");
      }
    } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
      $this->stderr("❌ Transport error: " . $e->getMessage() . "\n");
    } catch (\Throwable $e) {
      $this->stderr("❌ Error: " . $e->getMessage() . "\n");
    }
  }

  public function actionWsNotify($player)
  {
    $player=\app\modules\frontend\models\Player::findOne($player);
    $type = "info";
    $title="title";
    $body="body";
    $cc = true;
    $archive = true;
    $apiOnly = false;
    $player->notify($type, $title, $body, $cc, $archive, $apiOnly = false);
  }

}
