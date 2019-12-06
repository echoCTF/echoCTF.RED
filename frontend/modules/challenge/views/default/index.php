<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title = Yii::$app->sys->event_name.' - Challenges';
?>
<div class="challenge-index">
  <div class="body-content">
    <h2>
      <b><?= Html::encode($this->title)?></b>
      <span style="display: block;"><small clas="text-muted">Challenges for your consumption :)</small></span>
    </h2>
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
