<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title=Yii::$app->sys->event_name.' Tutorials';
$this->_description=$this->title
?>
<div class="tutorial-index">
  <div class="body-content">
    <h2><?= Html::encode($this->title)?></h2>
      Tutorials for your consumption :)
    <hr />
    <?php echo ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText'=>'<p class="text-info"><b>There are no tutorials available at the moment...</b></p>',
        'summary'=>false,
        'itemOptions' => [
          'tag' => false
        ],
        'itemView' => '_tutorial',
    ]);?>
  </div>
</div>
