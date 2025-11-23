<?php

use \yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::$app->sys->event_name . " " . \Yii::t('app', "Configure: {perkName}",['perkName'=>$model->product->name]);
$this->_url = \yii\helpers\Url::to([null], 'https');
//$this->_fluid = "-fluid";
?>
<div class="team-index">
  <div class="body-content">
    <h2><?= Html::encode($this->title) ?></h2>

  </div>
</div>