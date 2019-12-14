<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title = Html::encode(Yii::$app->sys->event_name. ' FAQ');
?>
<div class="faq-index">
  <div class="body-content">
    <h2><?=Html::encode($this->title)?></h2>
      Frequently Asked questions about the platform
    <hr />
    <?php echo ListView::widget([
        'dataProvider' => $dataProvider,
        'summary'=>false,
        'itemOptions' => [
          'tag' => false
        ],
        'itemView' => '_faq',
    ]);?>
  </div>
</div>
