<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title=Html::encode(Yii::$app->sys->event_name.' '.\Yii::t('app','Rules'));
$this->_description=\Yii::t('app','These rules are only in place to keep the platform entertaining and manageable. Please respect the rules and have fun :)');
$this->_url=\yii\helpers\Url::to(['index'], 'https');
use app\components\formatters\Anchor;
?>
<div class="rule-index">
  <div class="body-content">
  <h2><?=Html::encode($this->title)?></h2>
  <?=\Yii::t('app','These rules are only in place to keep the platform entertaining and manageable. Please respect the rules and have fun :)')?>
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
      'emptyText'=>'<p class="text-info"><b>'.\Yii::t('app','No rules exist at the moment...').'</b></p>',
      'summary'=>false,
      'itemOptions' => [
        'tag' => false
      ],
      'itemView' => '_rule',
  ]);?>
</div>
</div>
