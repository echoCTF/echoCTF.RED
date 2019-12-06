<?php
use yii\helpers\Html;
?>
<div class="leader">
    <div class="leader-wrap">
      <div class="leader-name" style="margin-bottom: 0.3em">
        <?=$model->formatted;?>, <?=$model->ts_ago?>
      </div>
    </div>
    <div class="border"></div>
</div>
