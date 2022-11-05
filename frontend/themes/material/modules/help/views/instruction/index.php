<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title=Html::encode(Yii::$app->sys->event_name.' '.\Yii::t('app','Instructions'));
$this->_description=\Yii::t('app','Instructions on connecting and getting help');
$this->_url=\yii\helpers\Url::to(['index'], 'https');
use app\components\formatters\Anchor;
?>
<div class="instruction-index">
  <div class="body-content">
  <h2><?=Html::encode($this->title)?></h2>
    <?=\Yii::t('app','Instructions on connecting and getting help')?>
  <hr />
  <?php if(intval($dataProvider->getCount())>0):?>
    <h4><?=\Yii::t('app','Table of Contents')?></h4>
    <ol>
    <?php foreach($dataProvider->getModels() as $entry):?>
      <li><?=Html::a(Html::encode($entry->title),'#'.Html::encode(Anchor::to($entry->title)));?></li>
    <?php endforeach;?>
    </ol>
  <?php endif;?>
  <?php echo ListView::widget([
      'dataProvider' => $dataProvider,
      'emptyText'=>'<p class="text-info"><b class="text-info">'.\Yii::t('app','No instructions exist at the moment...').'</b></p>',
      'summary'=>false,
      'itemOptions' => [
        'tag' => false
      ],
      'itemView' => '_instruction',
  ]);?>
</div>
</div>
