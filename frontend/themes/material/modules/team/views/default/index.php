<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ListView;
$this->loadLayoutOverrides=true;

$this->title = Yii::$app->sys->event_name . ' ' . \Yii::t('app', 'Teams');
$this->_fluid = "-fluid";
?>
<div class="team-index">
  <div class="body-content">
    <h2><?= Html::encode($this->title) ?></h2>
    <?php if (Yii::$app->user->identity->team === null) : ?>
      <?= \Yii::t('app', 'Join a team or <b>{createLink}</b> a new one!', ['createLink' => Html::a(\Yii::t('app', 'Create'), ['/team/default/create'], ['class' => 'btn btn-info btn-sm'])]) ?>
    <?php else : ?>
      <?= Html::a('Go to your Team', ['/team/default/view', 'token' => Yii::$app->user->identity->team->token], ['class' => 'btn block text-dark text-bold orbitron' . (!Yii::$app->user->identity->team->inviteonly ? ' btn-info' : ' btn-warning')]) ?>
    <?php endif; ?>
    <hr />
    <div class="row">
      <?php
      $colsCount = 3;
      echo ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => '<p class="text-warning"><b>' . \Yii::t('app', 'Oh! no, there are no teams... Quickly create one :)') . '</b></p>',
        'options' => ['tag' => false,],
        'itemOptions' => ['tag' => 'div','class'=>'col col-lg-4 col-md-6 col-sm-6 d-flex align-items-stretch'],
        'summary' => false,
        'itemView' => '_team_card',
        'viewParams' => ['invite' => false],
      ]);
      ?>
    </div>
  </div>
</div>