<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use app\widgets\stream\StreamWidget as Stream;

$this->title = Yii::$app->sys->event_name . ' Problem: ' . $problem->name . ' #' . $problem->id;
//$this->_description=$problem->description;
//$this->_image = \yii\helpers\Url::to($problem->fullLogo, 'https');
$this->_url = \yii\helpers\Url::to(['view', 'id' => $problem->id], 'https');
$this->_fluid = '-fluid';
?>
<h4 id="countdown"></h4>

<div class="target-index">
  <div class="body-content">

    <?php
    if (Yii::$app->user->isGuest)
      echo $this->render('_guest', ['problem' => $problem]);
    else {
      echo $this->render('_speed', ['speedForm' => $speedForm, 'problem' => $problem, 'identity' => Yii::$app->user->identity->profile]);
    }
    ?>

    <?php \yii\widgets\Pjax::begin(['id' => 'stream-listing', 'enablePushState' => false, 'linkSelector' => '#stream-pager a', 'formSelector' => false]); ?>
    <?php //echo Stream::widget(['divID' => 'target-activity', 'dataProvider' => $streamProvider, 'pagerID' => 'stream-pager', 'title' => 'Target activity', 'category' => 'Latest activity on the target']); ?>
    <?php \yii\widgets\Pjax::end(); ?>
  </div>
</div>
