<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title = Html::encode(Yii::$app->sys->event_name. ' Instructions');
$this->_description = 'Instructions on connecting and getting help';
?>
<div class="instruction-index">
  <div class="body-content">
  <h2><?=Html::encode($this->title)?></h2>
    Instructions on connecting and getting help
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
