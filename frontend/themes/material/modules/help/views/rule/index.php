<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title = Html::encode(Yii::$app->sys->event_name. ' Rules');
?>
<div class="rule-index">
  <div class="body-content">
  <h2><?=Html::encode($this->title)?></h2>
  These rules are only in place to keep the platform entertaining and manageable. Please respect the rules and have fun :)
  <hr />
  <?php echo ListView::widget([
      'dataProvider' => $dataProvider,
      'summary'=>false,
      'itemOptions' => [
        'tag' => false
      ],
      'itemView' => '_rule',
  ]);?>
</div>
</div>
