<?php
use yii\helpers\Html;
use app\widgets\target\HeadshotsWidget;
?>
<div class="card bg-dark headshots">
  <div class="card-header">
    <h4><i class="fas fa-skull"></i> <?= \Yii::t('app', '{headshots,plural,=0{No headshots yet} =1{# Headshot} other{# Headshots (newer first)}}', ['headshots' => count($target->headshots)]) ?>
    </h4>
  </div>
  <div class="card-body table-responsive">
    <?php echo HeadshotsWidget::widget(['target_id'=>$target->id]);?>
  </div>
</div>