<?php use yii\helpers\Html; ?>
<div class="card bg-dark headshots">
  <div class="card-header">
    <h4><i class="fas fa-skull"></i> <?= \Yii::t('app', '{headshots,plural,=0{No headshots yet} =1{# Headshot} other{# Headshots (newer first)}}', ['headshots' => count($target->headshots)]) ?>
    </h4>
  </div>
  <div class="card-body table-responsive">
    <?php
    $headshots = [];
    foreach ($target->lastHeadshots as $hs) {
      $to = $hs->playerWithProfile->profile->linkto;
      if ($to !== null) {
        $headshots[] = Html::a(Html::encode($hs->playerWithProfile->username), $to, ['data-pjax' => 0]);
      }
    }
    if (!empty($headshots)) {
      echo "<code>", implode(", ", array_slice($headshots, 0, 19)), "</code>";
      if (count($headshots) > 19) {
        echo "<details class=\"headshotters\">";
        echo "<summary data-open=\"Hide more\" data-close=\"Show more\"></summary>";
        echo "<code>", implode(", ", array_slice($headshots, 19)), "</code>";
        echo "</details>";
      }
    } else {
      echo '<code>' . \Yii::t('app', 'no one yet...') . '</code>';
    }
    ?>
  </div>
</div>