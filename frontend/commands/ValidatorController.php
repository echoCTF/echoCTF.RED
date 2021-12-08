<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\widgets\Table;
use yii\helpers\Console;
use app\modules\target\models\Target;
use app\models\Player;
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

  private function getErrorRows($model)
  {
    $errors=$model->getErrors();
    $errorows=null;
    foreach($errors as $field => $errstrs)
    {
      $errorows[]=[$model->{$field}, implode($errstrs," ")];
    }
    return $errorows;
  }
}
