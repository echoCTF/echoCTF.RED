<?php

namespace app\modules\activity\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\gameplay\models\Badge;
use app\modules\gameplay\models\Target;

/**
 * This is the model class for table "team_stream".
 *
 * @property int $team_id
 * @property string $model
 * @property int $model_id
 * @property float $points
 * @property string $ts
 */
class TeamStream extends TeamStreamAR
{

  const MODEL_ICONS = [
    'headshot' => '<i class="fas fa-skull" style="color: #FF1A00;font-size: 1.5em;" data-toggle="tooltip" title="Target Headshot"></i>',
    'challenge' => '<i class="fas fa-tasks" style="color: #FF1A00; font-size: 1.5em;" data-toggle="tooltip" title="Challenge Solve"></i>',
    'solution' => '<i class="fas fa-code" style="color: #FF1AFF; font-size: 1.5em;" title="Speed Programming Solution"></i>',
    'treasure' => '<i class="fas fa-flag text-danger" style="font-size: 1.5em;" data-toggle="tooltip" title="Target Flag"></i>',
    'finding' => '<i class="fas fa-fingerprint" style="color:#FF7400; font-size: 1.5em;" data-toggle="tooltip" title="Target Service"></i>',
    'question' => '<i class="fas fa-list-ul text-info" style="font-size: 1.5em;" data-toggle="tooltip" title="Challenge Question"></i>',
    'team_player' => '<i class="fas fa-users" style="font-size: 1.5em;"></i>',
    'user' => '<i class="fas fa-user-ninja " style="color: #4096EE;font-size: 1.5em;" data-toggle="tooltip" title="Player"></i>',
    'report' => '<i class="fas fa-clipboard-list" style="font-size: 1.5em;"></i>',
    'badge' => '<i class="fas fa-trophy" style="color: #C79810;font-size: 1.5em;" data-toggle="tooltip" title="Badge"></i>',
    'player_target_help' => '<i class="fas fa-book" style="color: #62c710ff;font-size: 1.5em;" data-toggle="tooltip" title="Activated Writeup"></i>'
  ];
  public $seconds_since_last;
  public $ts_ago;
  public $pub = true;

  public function getIcon()
  {
    return self::MODEL_ICONS[$this->model];
  }

  public function getPrefix($showIcon = true)
  {
    return "";
  }

  public function Title(bool $pub = true)
  {
    return $this->pub ? $this->pubtitle : $this->title;
  }

  public function getFormatted(bool $pub = true)
  {
    return $this->{$this->model . 'Message'};
  }

  public function getSuffix()
  {
    if ($this->points != 0)
      return sprintf(" for %d points", $this->points);
    return "";
  }

  public function getSolutionMessage()
  {
    return sprintf("%s %s%s", $this->prefix, $this->title, $this->suffix);
  }

  public function getBadgeMessage()
  {
    return sprintf("%s got the badge [<code>%s</code>]%s", $this->prefix, Badge::findOne(['id' => $this->model_id])->name, $this->suffix);
  }

  public function getPlayer_target_helpMessage()
  {
    return sprintf("%s activated writeups for [<code>%s</code>]", $this->getPrefix(false), Target::findOne(['id' => $this->model_id])->name);
  }

  public function getHeadshotMessage()
  {
    $headshot = \app\modules\activity\models\Headshot::findOne(['target_id' => $this->model_id]);
    if ($headshot->target->timer === 0 || $headshot->timer === 0)
      return sprintf("%s%s", Html::a(Target::findOne(['id' => $this->model_id])->name, ['/infrastructure/target/full-view', 'id' => $this->model_id]), $this->suffix);

    return sprintf("%s in <i data-toggle='tooltip' title='%s' class='fas fa-stopwatch'></i> %s minutes%s", Html::a(Target::findOne(['id' => $this->model_id])->name, ['/infrastructure/target/full-view', 'id' => $this->model_id]), Yii::$app->formatter->asDuration($headshot->timer), number_format($headshot->timer / 60), $this->suffix);
  }

  public function getChallengeMessage()
  {
    $csolver = \app\modules\activity\models\ChallengeSolver::findOne(['challenge_id' => $this->model_id]);
    return sprintf("%s managed to complete the challenge [<code>%s</code>]%s", $this->prefix, Html::a(\app\modules\gameplay\models\Challenge::findOne(['id' => $this->model_id])->name, ['/gameplay/challenge/view', 'id' => $this->model_id]), $this->suffix);

  }

  public function getReportMessage()
  {
    return sprintf("%s Reported <b>%s</b>%s", $this->prefix, $this->Title($this->pub), $this->suffix);
  }

  public function getQuestionMessage()
  {
    return sprintf("%s Answered the question of <b>%s</b> [%s] %s", $this->prefix, \app\modules\gameplay\models\Question::findOne($this->model_id)->challenge->name, \app\modules\gameplay\models\Question::findOne($this->model_id)->name, $this->suffix);
  }

  public function getFindingMessage()
  {
    return sprintf("%s %s", \app\modules\gameplay\models\Finding::findOne(['id'=>$this->model_id])->name, $this->suffix);
  }

  public function getTreasureMessage()
  {
    return sprintf("%s %s", \app\modules\gameplay\models\Treasure::findOne(['id'=>$this->model_id])->name, $this->suffix);
  }

  public function getUserMessage()
  {
    return $this->defaultMessage;
  }

  public function getDefaultMessage()
  {
    return sprintf("%s %s%s", $this->prefix, $this->Title($this->pub), $this->suffix);
  }
}
