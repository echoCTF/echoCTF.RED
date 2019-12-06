<div class="leader" >
    <div class="border"></div>
    <div class="leader-wrap">
      <div class="leader-place"><?=intval($index + 1);?>.</div>
      <div class="leader-name"><?=$model->player->profile->link?></div>
      <div class="leader-score_title"><?=number_format($model->points);?></div>
    </div>
    <div class="leader-bar">
      <div style="width: <?=round($model->points/$totalPoints*100)?>%" class="bar"></div>
    </div>
    <div class="border"></div>
</div>
