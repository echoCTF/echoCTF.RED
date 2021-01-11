<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title=Html::encode(Yii::$app->sys->event_name.' FAQ');
$this->_description='Frequently Asked questions about the platform';
$this->_url=\yii\helpers\Url::to(['index'], 'https');

?>
<div class="faq-index">
  <div class="body-content">
    <h2><?=Html::encode($this->title)?></h2>
      Frequently Asked questions about the platform
    <hr />
    <?php echo ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText'=>'<p class="text-info"><b>No FAQ entries exist at the moment...</b></p>',
        'summary'=>false,
        'itemOptions' => [
          'tag' => false
        ],
        'itemView' => '_faq',
    ]);?>
  </div>
</div>
