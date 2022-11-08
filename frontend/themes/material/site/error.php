<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */
use yii\helpers\Html;
use app\widgets\Noti;
if(property_exists($exception,'statusCode'))
  $error=\Yii::t('app','Error {statusCode}: ',['event_name'=>Yii::$app->sys->event_name,'statusCode'=>$exception->statusCode]). nl2br(Html::encode($message));
else
  $error=\Yii::t('app','Error: ',['event_name'=>Yii::$app->sys->event_name]). nl2br(Html::encode($message));

$this->title=Yii::$app->sys->event_name.' '.$error;
?>
<div class="site-error">
      <div class="row">
        <div class="col-sm-5">
          <img src="/images/ohnoes.svg" class="rounded img-fluid" align="right" width="260vw"/>
        </div>
        <div class="col">
          <h1 class="text-danger"><b><?=\Yii::t('app','Oh noes!!!')?></b></h1>
          <h3 class="text-warning"><?=\Yii::t('app','Look what you did...')?></h3>
          <h3 class="text-light"><?=$error?></h3>
        </div>
      </div>
      <center>
        <?=Html::a('<b><i class="fas fa-backward"></i> '.\Yii::t('app','Go back').'</b>',['/site/index'],['class'=>'btn btn-lg btn-primary text-dark'])?>
      </center>

</div>
