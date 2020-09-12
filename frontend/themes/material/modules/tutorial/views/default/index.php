<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title=Yii::$app->sys->event_name.' - Tutorials';
?>
<div class="tutorial-index">
  <div class="body-content">
    <h2>
      <b><?= Html::encode($this->title)?></b>
      <span style="display: block;"><small clas="text-muted">Tutorials for your consumption :)</small></span>
    </h2>
    <hr />
    <?php echo ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText'=>'<p class="text-warning"><b>There are no tutorials available at the moment...</b></p>',
        'summary'=>false,
        'itemOptions' => [
          'tag' => false
        ],
        'itemView' => '_tutorial',
    ]);?>
  </div>
</div>
