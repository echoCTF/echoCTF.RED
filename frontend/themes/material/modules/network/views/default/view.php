<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use app\widgets\stream\StreamWidget as Stream;
use app\widgets\target\TargetWidget;
$this->loadLayoutOverrides=true;
$this->title = Yii::$app->sys->event_name . ' ' . \Yii::t('app', 'Network details') . ' [' . Html::encode($model->name) . ']';
$this->_description = Html::encode(strip_tags($model->description));
$this->_image=\yii\helpers\Url::to($model->icon, 'https');
$this->_url = \yii\helpers\Url::to(['view', 'id' => $model->id], 'https');

$this->_fluid = "-fluid";
$module = \app\modules\network\Module::getInstance();

?>
<div class="network-view">
  <div class="body-content">
    <?php if ($module->checkNetwork($model) === false && !Yii::$app->user->isGuest) : ?>
      <div class="row d-flex justify-content-center">
        <div class="col-sm-12 col-md-6 col-xl-4 alert alert-danger d-flex justify-content-center" role="alert">
          <b><?= \Yii::t('app', "You don't have access to this network.") ?></b>
        </div>
      </div>
    <?php endif; ?>
    <div class="row">
      <div class="col">
      <h3><?= \Yii::t('app', 'Details for Network [<code>{network_name}</code>]', ['network_name' => Html::encode($model->name)]) ?></h3>
      </div>
      <div class="col">
        <div class="watermarked img-fluid">
          <img src="<?= $model->icon ?>" width="100px" />
        </div>
      </div>
    </div>
    <hr />
    <div class="row">
      <div class="col-md-8">
        <?php \yii\widgets\Pjax::begin(['id' => 'target-listing-pjax', 'enablePushState' => false, 'linkSelector' => '#target-pager a, #target-list th a', 'formSelector' => false]); ?>
        <?php
        if (Yii::$app->user->isGuest)
          echo TargetWidget::widget(['dataProvider' => $networkTargetProvider, 'title' => \Yii::t('app', 'Targets'), 'category' => \Yii::t('app', 'Network targets'), 'personal' => false, 'hidden_attributes' => ['id', 'progress', 'ip', 'writeup']]);
        else
          echo TargetWidget::widget(['dataProvider' => $networkTargetProvider, 'player_id' => Yii::$app->user->id, 'profile' => Yii::$app->user->identity->profile, 'title' => \Yii::t('app', 'Progress'), 'category' => \Yii::t('app', 'Network targets'), 'personal' => false, 'hidden_attributes' => ['id']]);
        ?>
        <?php \yii\widgets\Pjax::end() ?>

      </div>
      <div class="col-md-4">
        <div class="card card-profile bg-dark orbitron">
          <div class="card-body table-responsive">
            <h4 class="card-title orbitron text-bold"><?= Html::encode($model->name) ?></h4>
            <h6 class="badge badge-primary orbitron text-bold"><?= \Yii::t('app', '{targetsCount,plural,=0{no targets} =1{# target} other{# targets}}', ['targetsCount' => $model->targetsCount]) ?></h6>
            <p style="text-align: justify;" class="orbitron"><?= $model->description ?></p>
          </div>
<?php if(!Yii::$app->user->isGuest):?>
          <div class="card-footer orbitron justify-content-center"><?=Html::a(\Yii::t('app','Back to Networks'), ['/network/default/index'],['class'=>'btn btn-primary text-dark text-bold','title'=>\Yii::t('app','Back to Networks')])?></div>
<?php endif;?>
        </div>
      </div>

    </div>
  </div>
</div>