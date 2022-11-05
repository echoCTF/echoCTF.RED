<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title=Html::encode(Yii::$app->sys->event_name.' '.\Yii::t('app','Challenges'));
$this->_description=$this->title
?>
<div class="challenge-index">
  <div class="body-content">
    <h2><?=Html::encode($this->title)?></h2>
      <?=\Yii::t('app','Challenges for your consumption :)')?>
    <hr />
    <?php echo ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText'=>'<p class="text-info"><b>'.\Yii::t('app','There are no challenges available at the moment...').'</b></p>',
        'options'=>['class'=>'list-view row'],
        'summary'=>false,
        'itemOptions' => [
          'tag' => false,
        ],
        'itemView' => '_challenge',
    ]);?>
  </div>
</div>
