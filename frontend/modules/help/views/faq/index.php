<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title=Yii::$app->sys->event_name.' - FAQ';
$this->_description='Frequently Asked questions about the platform';
?>
<div class="faq-index">
  <div class="body-content">
    <h2>
      <b><?= Html::encode($this->title)?></b>
      <span style="display: block;"><small clas="text-muted">Frequently Asked questions about the platform</small></span>
    </h2>
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
