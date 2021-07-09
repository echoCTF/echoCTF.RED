<?php
use yii\helpers\Html;
use app\components\formatters\RankFormatter;
?>
<div class="leader" >
    <div class="border"></div>
    <div class="leader-wrap">
      <div class="leader-place"><?=$model->ordinalPlace;?>.</div>
      <div class="leader-ava"><img class="rounded <?=RankFormatter::ordinalPlaceCss($model->id)?>" src="<?=$model->avatar?>" width="30px"/></div>
      <div class="leader-name" style="width: 100%"><?=Html::encode($model->username)?></div>
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
