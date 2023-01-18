<?php
use yii\helpers\Html;
use app\components\formatters\RankFormatter;
?>
<div class="leader" >
    <div class="border"></div>
    <div class="leader-wrap">
      <div class="leader-place"><?=RankFormatter::ordinalPlace((($widget->dataProvider->pagination->getPage())*$widget->dataProvider->pagination->pageSize)+($index+1));?>.</div>
      <div class="leader-ava"><img src="/images/avatars/<?=$model->player->profile->avtr?>"  class="rounded <?=RankFormatter::ordinalPlaceCss((($widget->dataProvider->pagination->getPage())*$widget->dataProvider->pagination->pageSize)+($index+1));?>" width="30px"/></div>
      <div class="leader-name" style="width: 100%"><?=$model->player->profile->link?></div>
      <div class="leader-score_title" style="width: 100%"><?=number_format($model->timer);?></div>
    </div>
    <div class="leader-bar">
      <?php if(intval($totalPoints) === 0):?>
      <div style="width: 0%" class="bar"></div>
      <?php else: ?>
      <div style="width: <?=round($model->timer / $totalPoints * 100)?>%" class="bar"></div>
      <?php endif;?>
    </div>

    <div class="border"></div>
</div>
