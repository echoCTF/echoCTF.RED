<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title = Html::encode(Yii::$app->sys->event_name. ' Challenges');
?>
<div class="challenge-index">
  <div class="body-content">
    <h2><?=Html::encode($this->title)?></h2>
      Challenges for your consumption :)

    <hr />
    <?php echo ListView::widget([
        'dataProvider' => $dataProvider,
        'summary'=>false,
        'itemOptions' => [
          'tag' => false
        ],
        'itemView' => '_challenge',
    ]);?>
  </div>
</div>
