<?php

namespace app\modules\activity\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\gameplay\models\Badge;
use app\modules\gameplay\models\Target;
/**
 * This is the model class for table "stream" implementing frontend methods.
 *
 * @property string $icon
 * @property string $prefix
 * @property string $suffix
 * @property string $defaultMessage
 *
 */
class Stream extends StreamAR
{

  const MODEL_ICONS=[
    'headshot'=>'<i class="fas fa-skull" style="color: #FF1A00;font-size: 1.5em;" data-toggle="tooltip" title="Target Headshot"></i>',
    'challenge'=>'<i class="fas fa-tasks" style="color: #FF1A00; font-size: 1.5em;" data-toggle="tooltip" title="Challenge Solve"></i>',
    'treasure'=>'<i class="fas fa-flag text-danger" style="font-size: 1.5em;" data-toggle="tooltip" title="Target Flag"></i>',
    'finding'=>'<i class="fas fa-fingerprint" style="color:#FF7400; font-size: 1.5em;" data-toggle="tooltip" title="Target Service"></i>',
    'question'=>'<i class="fas fa-list-ul text-info" style="font-size: 1.5em;" data-toggle="tooltip" title="Challenge Question"></i>',
    'team_player'=>'<i class="fas fa-users" style="font-size: 1.5em;"></i>',
    'user'=>'<i class="fas fa-user-ninja " style="color: #4096EE;font-size: 1.5em;" data-toggle="tooltip" title="Player"></i>',
    'report'=>'<i class="fas fa-clipboard-list" style="font-size: 1.5em;"></i>',
    'badge'=>'<i class="fas fa-trophy" style="color: #C79810;font-size: 1.5em;" data-toggle="tooltip" title="Badge"></i>',
  ];

  public $ts_ago;
  public $pub=true;
  public function getIcon()
  {
    return self::MODEL_ICONS[$this->model];
  }

  public function getPrefix()
  {
    return sprintf("<img src='%s' class='rounded' width='25px'> <b>%s</b> %s", Url::to('//'.Yii::$app->sys->offense_domain.'/images/avatars/'.$this->player->profile->avtr),$this->player->profile->link,$this->icon);
  }

  public function Title(bool $pub=true)
  {
    return $this->pub ? $this->pubtitle : $this->title;
  }

  public function getFormatted(bool $pub=true)
  {
    if(!Yii::$app->user->isGuest && (Yii::$app->user->id === $this->player_id || Yii::$app->user->identity->isAdmin))
      $this->pub=false;
    return $this->{$this->model.'Message'};
  }

  public function getSuffix()
  {
    if($this->points != 0)
      return sprintf(" for %d points", $this->points);
    return "";
  }

  public function getBadgeMessage()
  {
      return sprintf("%s got the badge [<code>%s</code>]%s", $this->prefix, Badge::findOne(['id'=>$this->model_id])->name, $this->suffix);
  }


  public function getHeadshotMessage()
  {
    $headshot=\app\modules\activity\models\Headshot::findOne(['target_id'=>$this->model_id, 'player_id'=>$this->player_id]);
    if($headshot->target->timer===0 || $headshot->timer===0)
      return sprintf("%s managed to headshot [<code>%s</code>]%s", $this->prefix, Html::a(Target::findOne(['id'=>$this->model_id])->name, ['/target/default/index', 'id'=>$this->model_id]), $this->suffix);

    return sprintf("%s managed to headshot [<code>%s</code>] in <i data-toggle='tooltip' title='%s' class='fas fa-stopwatch'></i> %s minutes%s", $this->prefix, Html::a(Target::findOne(['id'=>$this->model_id])->name, ['/target/default/index', 'id'=>$this->model_id]), Yii::$app->formatter->asDuration($headshot->timer), number_format($headshot->timer / 60), $this->suffix);
  }

  public function getChallengeMessage()
  {
    $csolver=\app\modules\activity\models\ChallengeSolver::findOne(['challenge_id'=>$this->model_id, 'player_id'=>$this->player_id]);
    if($csolver->challenge->timer===0)
      return sprintf("%s managed to complete the challenge [<code>%s</code>]%s", $this->prefix, Html::a(\app\modules\gameplay\models\Challenge::findOne(['id'=>$this->model_id])->name, ['/challenge/default/view', 'id'=>$this->model_id]), $this->suffix);

    return sprintf("%s managed to complete the challenge [<code>%s</code>] in <i data-toggle='tooltip' title='%s' class='fas fa-stopwatch'></i> %s minutes%s", $this->prefix, Html::a(\app\modules\gameplay\models\Challenge::findOne(['id'=>$this->model_id])->name, ['/challenge/default/view', 'id'=>$this->model_id]), Yii::$app->formatter->asDuration($csolver->timer),number_format($csolver->timer / 60), $this->suffix);
  }

  public function getReportMessage()
  {
    return sprintf("%s Reported <b>%s</b>%s", $this->prefix, $this->Title($this->pub), $this->suffix);
  }

  public function getQuestionMessage()
  {
    return sprintf("%s Answered the question of <b>%s</b> [%s] %s", $this->prefix, \app\modules\gameplay\models\Question::findOne($this->model_id)->challenge->name,\app\modules\gameplay\models\Question::findOne($this->model_id)->name, $this->suffix);
  }

  public function getFindingMessage()
  {
    return $this->defaultMessage;
  }

  public function getTreasureMessage()
  {
    return $this->defaultMessage;
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
