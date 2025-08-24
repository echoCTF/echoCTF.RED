<?php
use app\components\formatters\RankFormatter;
use yii\helpers\Html;
?>

<div class="leader <?=intval($player_id) === intval($model->player_id) ? "bg-dark text-primary" : ""?>" >
    <div class="border"></div>
    <div class="leader-wrap">
      <div class="leader-place"><?=$model->ordinalPlace;?>.</div>
      <div class="leader-ava"><img src="<?=$model->avatar?>" class="rounded <?=RankFormatter::ordinalPlaceCss($model->id)?>" width="25px"/></div>
<?php if(\Yii::$app->user->isGuest):?>
      <div class="leader-name"><?=Html::encode($model->player->username)?></div>
<?php else: ?>
      <div class="leader-name"><?=$model->player->profile->link?></div>
<?php endif; ?>
      <div class="leader-score_title"><?=number_format($model->score->points);?></div>
    </div>
    <div class="leader-bar">
      <?php if(intval($totalPoints) === 0):?>
      <div style="width: 0%" class="bar"></div>
      <?php else: ?>
      <div style="width: <?=round($model->score->points / $totalPoints * 100)?>%" class="bar"></div>
      <?php endif;?>
    </div>
    <div class="border"></div>
</div>
