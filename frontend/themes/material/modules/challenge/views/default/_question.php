<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
?>
<div class="question-view">
  <details>
  <?php echo $model->description; ?>
  <summary><b id="question-<?=$index+1?>">Q<?php echo $index+1?>: <?php echo Html::encode($model->name); ?> (<?php echo $model->points?>) <?php if($model->answered!=null):?><i class="glyphicon glyphicon-ok"></i><?php endif;?></b></summary>
  </details>
</div>
