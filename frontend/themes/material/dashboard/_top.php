<?php

use app\widgets\Card;
use yii\widgets\ListView;
?>
<div class="row justify-content-center">
  <div class="col-xl-3 col-lg-6 col-md-6 col-sm-8">
    <?php Card::begin([
      'header' => 'header-icon',
      'type' => 'card-stats',
      'icon' => '<i class="fas fa-flag"></i>',
      'color' => 'target',
      'title' => number_format($dashboardStats->claims) /*sprintf('%d / %d', $treasureStats->claimed, $treasureStats->total)*/,
      'subtitle' => \Yii::t('app', 'Flag Claims'),
      'footer' => '<div class="stats"></div>',
    ]);
    Card::end(); ?>
    <?php Card::begin([
      'header' => 'header-icon',
      'type' => 'card-stats',
      'icon' => '<i class="fas fa-chart-line"></i>',
      'color' => 'activities',
      'title' => number_format(\app\models\Stream::find()->count()),
      'subtitle' => \Yii::t('app', 'Activities'),
      'footer' => '<div class="stats"></div>',
    ]);
    Card::end(); ?>
  </div>
  <?php if ($newsProvider->getTotalCount() > 0) : ?>
    <div class="col-lg-6 col-xl-6">
      <div class="card bg-dark" style="margin-top:0px;">
        <div class="card-body">
          <h3 class="card-title text-center"><?= \Yii::t('app', 'Latest News') ?></h3>
          <?php
          echo ListView::widget([
            'layout' => '{items}',
            'dataProvider' => $newsProvider,
            'itemView' => '_news_item',
          ]);
          ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <div class="col-xl-3 col-lg-6 col-md-6 col-sm-8">
    <?php Card::begin([
      'header' => 'header-icon',
      'type' => 'card-stats',
      'icon' => '<i class="fas fa-globe"></i>',
      'color' => 'countries',
      'title' => sprintf('%d', $dashboardStats->countries),
      'subtitle' => \Yii::t('app', 'Countries'),
      'footer' => '<div class="stats"></div>',
    ]);
    Card::end(); ?>
    <?php Card::begin([
      'type' => 'card-stats',
      'header' => 'header-icon',
      'icon' => '<i class="fas fa-user-secret"></i>',
      'color' => 'users',
      'title' => \app\models\Player::find()->active()->count(),
      'subtitle' => \Yii::t('app', 'Users'),
      'footer' => '<div class="stats"></div>',
    ]);
    Card::end(); ?>
  </div>
</div>