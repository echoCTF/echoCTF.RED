<?php

use app\widgets\Card;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\widgets\Twitter;
use app\modules\speedprogramming\models\SpeedSolution;
use yii\helpers\Markdown;

$this->registerJsFile('@web/js/plugins/bootstrap-selectpicker.min.js', ['depends' => ['app\assets\MaterialAsset']]);
$this->registerCssFile("@web/css/bootstrap-select.min.css", [
  'depends' => ['app\assets\MaterialAsset'],
], 'css-print-theme');
$this->registerJs('$.fn.selectpicker.Constructor.BootstrapVersion = "4";');

$headshot_icon = 'fa-skull-crossbones';
$noheadshot_icon = 'fa-not-equal';
$player_timer = '';
$twmsg = sprintf('Hey check this out, %s found the solution of [%s]', $identity->isMine ? "I" : $identity->twitterHandle, $problem->name);
?>
<div class="row">
  <div class="col-xl-4 col-lg-5 col-md-5 col-sm-12">
    <?php Card::begin([
      'header' => 'header-icon',
      'type' => 'card-stats',
      'icon' => sprintf('<img src="/images/problems/_%s-thumbnail.png" class="img-fluid" style="max-width: 10rem; max-height: 4rem;"/>', $problem->id),
      'color' => 'warning',
      //'subtitle' => sprintf("(%s)", ucfirst($problem->difficultyText)),
      'title' => sprintf('%s (%s)', $problem->name,ucfirst($problem->difficultyText)),
    ]);
    Card::end(); ?>
  </div>
  <div class="col-xl-4 col-lg-2 col-md-2 col-sm-12 text-center">

  </div>
  <div class="col-xl-4 col-lg-5 col-md-5 col-sm-12">
    <?php Card::begin([
      'header' => 'header-icon',
      'type' => 'card-stats',
      'icon' => sprintf('<img src="/images/avatars/%s" height="60"/>', $identity->avtr),
      'color' => 'primary',
      'title' => $identity->owner->username . " / " . $identity->rank->ordinalPlace . " Place",
      'footer' => sprintf('<div class="stats">%s %s</div>', Twitter::widget([
        'message' => $twmsg,
        'linkOptions' => ['class' => 'target-view-tweet', 'target' => '_blank', 'style' => 'font-size: 1.4em;', 'rel' => 'noopener noreferrer nofollow'],
      ]), Html::encode($identity->bio)),
    ]);
    Card::end(); ?>
  </div>
</div>
<div class="row">
  <div class="col">
    <div class="card terminal">
      <div class="card-body">
        <?php if (SpeedSolution::findOne(['player_id' => Yii::$app->user->id, 'problem_id' => $problem->id]) === null): ?>
          <?= $this->render('_speed_form', ['model' => $speedForm, 'problem' => $problem, 'identity' => $identity]); ?>
        <?php elseif (SpeedSolution::findOne(['player_id' => Yii::$app->user->id, 'problem_id' => $problem->id]) !== null): ?>
          <?= $this->render('_speed_grid', ['problem' => $problem, 'identity' => $identity]); ?>
        <?php endif; ?>
        <?= $problem->description ?>
      </div>
    </div>

  </div>
</div>