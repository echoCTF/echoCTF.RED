<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title=Html::encode(Yii::$app->sys->event_name.' '.\Yii::t('app','Experience Levels'));
$this->_description=\Yii::t('app','Experience levels and their requirements in points');
$this->_url=\yii\helpers\Url::to(['index'], 'https');
?>
<div class="faq-index">
  <div class="body-content">
    <h2><?=Html::encode($this->title)?></h2>
      <?=\Yii::t('app','The experience levels and their point ranges.')?>
    <hr />
    <?php if(intval($dataProvider->getCount())>0):?>
      <h4><?=\Yii::t('app','Table of Contents')?></h4>
      <ol class="orbitron text-white">
      <?php foreach($dataProvider->getModels() as $entry):?>
        <li><?=Html::encode($entry->name);?> (<code><?=number_format($entry->min_points)?>-<?=number_format($entry->max_points)?></code>): <small><?=number_format(($entry->player_count/$totalPlayers)*100,2)?>% of Players</small></li>
      <?php endforeach;?>
      </ol>
    <?php endif;?>
  </div>
</div>
