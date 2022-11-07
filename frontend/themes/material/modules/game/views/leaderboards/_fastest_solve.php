<?php
use yii\helpers\Html;
use app\components\formatters\RankFormatter;
?>
<div class="leader" >
    <div class="border"></div>
    <div class="leader-wrap">
      <div class="leader-place"><?=RankFormatter::ordinalPlace((($widget->dataProvider->pagination->getPage())*$widget->dataProvider->pagination->pageSize)+($index+1));?>.</div>
      <div class="leader-ava"><img src="/images/avatars/<?=$model->player->profile->avtr?>"  class="rounded <?=RankFormatter::ordinalPlaceCss((($widget->dataProvider->pagination->getPage())*$widget->dataProvider->pagination->pageSize)+($index+1));?>" width="30px"/></div>
      <div class="leader-name" style="width: 100%"><?=$model->player->profile->link?> <?=\Yii::t('app','on')?> <?=Html::a($model->challenge->name,['/challenge/default/view','id'=>$model->challenge_id]);?></div>
      <div class="leader-score_title" style="width: 50px"><?=number_format($model->timer);?></div>
    </div>
    <div class="leader-bar"><div style="width: 0%" class="bar"></div></div>
    <div class="border"></div>
</div>
