<?php
use yii\helpers\Html;
use app\components\formatters\RankFormatter;
?>
<div class="leader" >
    <div class="border"></div>
    <div class="leader-wrap">
      <div class="leader-place"><?=RankFormatter::ordinalPlace((($widget->dataProvider->pagination->getPage())*$widget->dataProvider->pagination->pageSize)+($index+1));?>.</div>
      <div class="leader-ava"><img src="/images/avatars/<?=Html::encode($model->player->profile->avtr)?>"  class="rounded <?=RankFormatter::ordinalPlaceCss((($widget->dataProvider->pagination->getPage())*$widget->dataProvider->pagination->pageSize)+($index+1));?>" width="30px"/></div>
      <div class="leader-name" style="width: 100%"><?=$model->player->profile->link?></div>
      <div class="leader-score_title" style="width: 100%"><abbr title="<?=\Yii::t('app', 'Current / best (weeks)')?>"><?=number_format($model->current);?> / <?=number_format($model->best);?></abbr></div>
    </div>
    <div class="leader-bar">
      <div style="width: <?=$model->best > 0 ? round($model->current / $model->best * 100) : 0?>%" class="bar"></div>
    </div>

    <div class="border"></div>
</div>
