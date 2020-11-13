<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title=Html::encode(Yii::$app->sys->event_name.' Challenges');
$this->_description=$this->title
?>
<div class="challenge-index">
  <div class="body-content">
    <h2><?=Html::encode($this->title)?></h2>
      Challenges for your consumption :)
    <hr />
    <?php echo ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText'=>'<p class="text-info"><b>There are no challenges available at the moment...</b></p>',
        'summary'=>false,
        'itemOptions' => [
          'tag' => false
        ],
        'itemView' => '_challenge',
    ]);?>
  </div>
</div>
