<?php
use yii\helpers\Html;
use app\components\formatters\RankFormatter;
?>
<div class="leader" >
    <div class="border"></div>
    <div class="leader-wrap">
      <div class="leader-place"><?=RankFormatter::ordinalPlace(RankFormatter::ordinalPlaceByPagination($dataProvider,$index))?>.</div>
      <div class="leader-ava"><img src="/images/flags/flat/64/<?=$model->country?>.png"  class="rounded <?=RankFormatter::ordinalPlaceCss($model->id)?>" width="30px"/></div>
      <div class="leader-name"><?=$model->countryObj->name?></div>
      <div class="leader-score_title"><?=number_format($model->counter);?></div>
    </div>
    <div class="leader-bar">
      <?php if(intval($totalPoints) === 0):?>
      <div style="width: 0%" class="bar"></div>
      <?php else: ?>
      <div style="width: <?=round($model->counter / $totalPoints * 100)?>%" class="bar"></div>
      <?php endif;?>
    </div>
    <div class="border"></div>
</div>
