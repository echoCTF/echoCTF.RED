<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title=Html::encode(Yii::$app->sys->event_name.' Rules');
$this->_description='These rules are only in place to keep the platform entertaining and manageable. Please respect the rules and have fun :)';
$this->_url=\yii\helpers\Url::to(['index'], 'https');
?>
<div class="rule-index">
  <div class="body-content">
  <h2><?=Html::encode($this->title)?></h2>
  These rules are only in place to keep the platform entertaining and manageable. Please respect the rules and have fun :)
  <hr />
  <?php echo ListView::widget([
      'dataProvider' => $dataProvider,
      'emptyText'=>'<p class="text-info"><b>No rules exist at the moment...</b></p>',
      'summary'=>false,
      'itemOptions' => [
        'tag' => false
      ],
      'itemView' => '_rule',
  ]);?>
</div>
</div>
