<?php
use yii\helpers\Html;
use app\components\formatters\RankFormatter;
?>
<div class="leader" >
    <div class="border"></div>
    <div class="leader-wrap">
      <div class="leader-place"><?=RankFormatter::ordinalPlace((($widget->dataProvider->pagination->getPage())*$widget->dataProvider->pagination->pageSize)+($index+1));?>.</div>
      <div class="leader-ava"><img src="<?=$model->avatar?>"  class="rounded <?=RankFormatter::ordinalPlaceCss((($widget->dataProvider->pagination->getPage())*$widget->dataProvider->pagination->pageSize)+($index+1))?>" width="30px"/></div>
      <div class="leader-name"><?=$model->player->profile->link?></div>
      <div class="leader-score_title"><?=number_format($model->points);?></div>
    </div>
    <div class="leader-bar">
      <?php if(intval($totalPoints) === 0):?>
      <div style="width: 0%" class="bar"></div>
      <?php else: ?>
      <div style="width: <?=round($model->points / $totalPoints * 100)?>%" class="bar"></div>
      <?php endif;?>
    </div>
    <div class="border"></div>
</div>
