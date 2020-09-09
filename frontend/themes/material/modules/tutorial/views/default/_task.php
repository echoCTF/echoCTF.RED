<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
?>
<div class="task-view">
  <details>
  <?php echo $model->description;?>
  <summary><b id="task-<?=$index + 1?>">Q<?php echo $index + 1?>: <?php echo Html::encode($model->title);?> (<?php echo $model->points?>)</b></summary>
  </details>
</div>
