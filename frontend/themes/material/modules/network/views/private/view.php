<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use app\widgets\stream\StreamWidget as Stream;
use app\widgets\target\TargetWidget;

$this->loadLayoutOverrides = true;
$this->title = \Yii::t('app', "{event_name} {username}'s Private Network details", ['username' => $model->player->username, 'event_name' => Yii::$app->sys->event_name]);
$this->_description = \Yii::t('app', "{event_name} {username}'s Private Network details", ['username' => $model->player->username, 'event_name' => Yii::$app->sys->event_name]);
$this->_url = \yii\helpers\Url::to(['view', 'id' => $model->id], 'https');

$this->_fluid = "-fluid";
$module = \app\modules\network\Module::getInstance();

?>
<div class="network-view">
  <div class="body-content">
    <div class="row">
      <div class="col">
        <h3><?= \Yii::t('app', 'Details for {username}\'s private network', ['username' => Html::encode($model->player->username)]) ?></h3>
      </div>
      <div class="col">
        <div class="watermarked img-fluid">
          <img src="/images/extras.png" width="100px" />
        </div>
      </div>
    </div>
    <hr />
    <div class="row">
      <div class="col-md-8">
        <?php \yii\widgets\Pjax::begin(['id' => 'target-listing-pjax', 'enablePushState' => false, 'linkSelector' => '#target-pager a, #target-list th a', 'formSelector' => false]); ?>
        <?= TargetWidget::widget(['dataProvider' => $networkTargetProvider, 'player_id' => Yii::$app->user->id, 'profile' => Yii::$app->user->identity->profile, 'title' => \Yii::t('app', 'Progress'), 'category' => \Yii::t('app', 'Network targets'), 'personal' => false, 'hidden_attributes' => ['id']]); ?>
        <?php \yii\widgets\Pjax::end() ?>
      </div>
      <div class="col-md-4">
        <div class="card card-profile bg-dark orbitron">
          <div class="card-body table-responsive">
            <h4 class="card-title orbitron text-bold"><?= \Yii::t('app', '{username}\'s private network', ['username' => Html::encode($model->player->username)]) ?></h4>
            <h6 class="badge badge-primary orbitron text-bold"><?= \Yii::t('app', '{targetsCount,plural,=0{no targets} =1{# target} other{# targets}}', ['targetsCount' => $model->getPrivateTargets()->count()]) ?></h6>
            <p style="text-align: justify;" class="orbitron">This network belongs to <?= Html::encode($model->player->username) ?>
              <?php if ($model->team_accessible === 1): ?> and members of their team<?php endif; ?>.</p>
          </div>
          <div class="card-footer orbitron justify-content-center"><?= Html::a(\Yii::t('app', 'Go Back'), Yii::$app->request->referrer ?: Yii::$app->homeUrl, ['class' => 'btn btn-primary text-dark text-bold', 'title' => \Yii::t('app', 'Go Back')]) ?></div>
        </div>
      </div>

    </div>
  </div>
</div>