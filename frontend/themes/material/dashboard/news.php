<?php

use yii\widgets\ListView;
use yii\helpers\HtmlPurifier;
use yii\helpers\Html;

$this->_fluid = "-fluid";
$this->loadLayoutOverrides = true;
$this->title = Yii::$app->sys->event_name . ' News article';
$this->_description = \Yii::t('app', "News entry");

?>
<div class="news-index">
  <div class="body-content">
    <h3 class="text-warning"><?= $model->category; ?> <?= HtmlPurifier::process($model->title) ?></h3>
    <div class="row">
      <div class="col-sm-6">
        <?= Yii::$app->formatter->asMarkdown($model->body) ?>

        <h5>Posted: <span style="color: lightgray; font-size: 0.8em"><?= Yii::$app->formatter->asDate($model->created_at) ?></span></h5>
        <center>
          <?= Html::a('<b><i class="fas fa-backward"></i> ' . \Yii::t('app', 'Go back') . '</b>', ['/site/index'], ['class' => 'btn btn-lg btn-primary text-dark']) ?>
        </center>
      </div>
    </div>
  </div>
</div>