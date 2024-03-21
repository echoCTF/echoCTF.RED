<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\widgets\Table;
use yii\helpers\Console;
use app\modules\target\models\Target;
use app\models\Player;
use app\modules\team\models\Team;
use app\models\Profile;
use app\models\Stream;
class ValidatorController extends Controller {

  public function actionIndex()
  {
    $this->stdout("The Validator provides the following actions: \n", Console::BOLD);
    echo Table::widget([
      'headers' => ['Command', 'Description'],
      'rows' => [
          ['validator/player', 'Validate player details'],
          ['validator/profile', 'Validate profile details'],
          ['validator/player-avatars', 'Validate player avatars and optionally remove stalled images'],
          ['validator/team-avatars', 'Validate team avatars and optionally remove stalled images'],
          ['validator/test-registration', 'Validate registration test'],
      ],
    ]);
    return ExitCode::OK;
  }

  public function actionPlayer()
  {
    foreach(Player::find()->all() as $player)
    {
      if(!$player->validate())
      {
        echo Table::widget([
          'headers' =>['Error for ID: '.$player->id, 'Description'],
          'rows' => $this->getErrorRows($player),
        ]);
      }
    }
  }

  public function actionProfile()
  {
    foreach(Profile::find()->all() as $profile)
    {
      $profile->scenario='validator';
      if(!$profile->validate())
      {
        echo Table::widget([
          'headers' =>['Error for ID: '.$profile->id, 'Description'],
          'rows' => $this->getErrorRows($profile),
        ]);
      }
    }
  }

  /**
  * Test Registration Validators
  */
  public function actionTestRegistration($email,$signup_ip)
  {
    $StopForumSpam = new \app\components\validators\StopForumSpamValidator();
    $HourValidator = new \app\components\validators\HourRegistrationValidator(['client_ip'=>$signup_ip,'counter'=>3]);
    $OverallValidator = new \app\components\validators\TotalRegistrationsValidator(['client_ip'=>$signup_ip,'counter'=>10]);
    $WhoisValidator = new \app\components\validators\WhoisValidator();
    $MxValidator = new \app\components\validators\MXServersValidator();
    if (!$StopForumSpam->validate($email, $error)) {
        echo "stopforum:",$error,"\n";
    }
    if (!$HourValidator->validate($signup_ip, $error)) {
        echo "hourvalidator:",$error,"\n";
    }
    if (!$OverallValidator->validate($signup_ip, $error)) {
        echo "overall:",$error,"\n";
    }
    if (!$WhoisValidator->validate($email, $error)) {
        echo "whois:",$error,"\n";
    }

    if (!$MxValidator->validate($email, $error)) {
        echo "mx:",$error,"\n";
    }
  }

  public function actionPlayerAvatars($remove=false)
  {
    $base=\Yii::getAlias('@app/web/images/avatars');
    foreach(glob("$base/[0-9]*.png") as $path)
    {
      $id=intval(basename(basename($path),'.png'));
      if(Profile::findOne($id)===null && $remove!==false)
      {
        echo "deleting $path\n";
        unlink($path);
        @unlink(sprintf("%s/badges/%d.png",$base,$id));
      }
    }
  }

  public function actionTeamAvatars($remove=false)
  {
    $base=\Yii::getAlias('@app/web/images/avatars/team');
    foreach(glob("$base/[0-9]*.png") as $path)
    {
      $id=intval(basename(basename($path),'.png'));
      if(Team::findOne($id)===null && $remove!==false)
      {
        echo "deleting $path\n";
        unlink($path);
      }
    }
  }

  private function getErrorRows($model)
  {
    $errors=$model->getErrors();
    $errorows=null;
    foreach($errors as $field => $errstrs)
    {
      $errorows[]=[$model->{$field}, implode(" ",$errstrs)];
    }
    return $errorows;
  }
}
