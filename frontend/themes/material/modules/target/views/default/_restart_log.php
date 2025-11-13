<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
?>
<div class="card bg-dark">
  <div class="card-header">
    <h4><i class="fas fa-sync"></i> <?= \Yii::t('app', 'Reboot log') ?></h4>
  </div>
  <div class="card-body table-responsive">
    <?php foreach ($target->spinHistories as $entry): ?>
      <div class="leader">
        <div class="leader-name"><?= $entry->player->profile->link ?> <?= Yii::$app->formatter->asRelativeTime($entry->created_at) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>