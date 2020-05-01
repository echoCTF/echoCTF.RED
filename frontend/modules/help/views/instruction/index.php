<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title=Yii::$app->sys->event_name.' - Instructions';
?>
<div class="instruction-index">
  <div class="body-content">
  <h2>
    <b><?= Html::encode($this->title)?></b>
    <span style="display: block;"><small clas="text-muted">Instructions on connecting and asking for help</small></span>
  </h2>
  <hr />
  <?php echo ListView::widget([
      'dataProvider' => $dataProvider,
      'summary'=>false,
      'itemOptions' => [
        'tag' => false
      ],
      'itemView' => '_instruction',
  ]);?>
</div>
</div>
