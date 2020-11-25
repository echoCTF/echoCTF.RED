<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title=Html::encode(Yii::$app->sys->event_name.' Networks');
$this->_description=$this->title;

?>
<div class="network-index">
  <div class="body-content">
    <h2><?=Html::encode($this->title)?></h2>
    Networks consist of multiple targets that are grouped together to represent more complicated installations and setups or simply group a specific type of targets into groups.
    <hr />
    <div class="row">
      <?php echo ListView::widget([
          'dataProvider' => $dataProvider,
          'emptyText'=>'<p class="text-info"><b>There are no networks available at the moment...</b></p>',
          'summary'=>false,
          'options' => ['tag' => false],
          'itemOptions' => ['tag' => false],
          'itemOptions' => [
            'tag' => false
          ],
          'itemView' => '_network',
      ]);?>
    </div>
  </div>
</div>
