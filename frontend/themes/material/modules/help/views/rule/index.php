<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title = Yii::$app->sys->event_name.' - Rules';
?>
<div class="rule-index">
  <div class="body-content">
  <h2><b><?= Html::encode($this->title)?></b><span style="display: block;"><small clas="text-muted">These rules are only in place to keep the platform entertaining and manageable. Please respect the rules and have fun :)</small></span></h2>
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
