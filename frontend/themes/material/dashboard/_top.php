<?php
use yii\widgets\Pjax;
use app\widgets\Card;
use yii\widgets\ListView;
use yii\helpers\Html;
?>
<div class="row justify-content-center">
  <div class="col col-xl-4" style="max-width: 333px;">
    <div class="card bg-dark" style="margin-top:0px;">
      <div class="card-body">
        <h3 class="card-title text-center" data-toggle="tooltip" title="Last 5 targets you visited" style="margin-bottom: 0.9em;"><?= \Yii::t('app', 'Last visits') ?></h3>
        <?= ListView::widget([
          'layout' => '{items}',
          'emptyText' => 'No targets visited yet.',
          'options' => ['class' => "list-group list-group-flush"],
          'dataProvider' => $lastVisitsProvider,
          'itemView' => '_last_visit_item',
        ]); ?>
      </div>
    </div>
    <?php if (Yii::$app->user->identity->instance && Yii::$app->user->identity->instance->target) : ?>
      <?= $this->render('_target_instance_card'); ?>
    <?php endif; ?>
  </div>

  <?php if ($newsProvider->getTotalCount() > 0) : ?>
    <div class="col-lg-6 col-xl-5">
      <div class="card bg-dark" style="margin-top:0px;">
        <div class="card-body">
          <h3 class="card-title text-center"><i class="fas fa-newspaper"></i> <?= \Yii::t('app', 'Latest News') ?></h3>
          <?php
          Pjax::begin(['id' => 'dashboardNews', 'enablePushState' => false, 'linkSelector' => '#news-pager a', 'formSelector' => false]);

          echo ListView::widget([
            'id' => 'dashboardNews',
            'layout' => '{items}<div class="card-footer">{pager}</div>',
            'dataProvider' => $newsProvider,
            'itemView' => '_news_item',
            'viewParams' => ['full' => false],
            'pager' => [
              'class' => 'yii\bootstrap4\LinkPager',
              'options' => ['class' => 'd-flex align-items-end justify-content-between', 'id' => 'news-pager'],
              'linkOptions' => ['class' => ['page-link'], 'aria-label' => 'Pager link', 'rel' => 'nofollow'],
              'firstPageLabel' => '<i class="fas fa-step-backward"></i>',
              'lastPageLabel' => '<i class="fas fa-step-forward"></i>',
              'maxButtonCount' => 3,
              'disableCurrentPageButton' => true,
              'prevPageLabel' => '<i class="fas fa-chevron-left"></i>',
              'nextPageLabel' => '<i class="fas fa-chevron-right"></i>',
            ],

          ]);

          Pjax::end();
          ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8">
    <?php Card::begin([
      'header' => 'header-icon',
      'type' => 'card-stats bg-dark',
      'icon' => '<i class="fas fa-globe"></i>',
      'color' => 'countries',
      'title' => sprintf('%d', $dashboardStats->countries),
      'subtitle' => \Yii::t('app', 'Countries'),
      'footer' => false,
    ]);
    Card::end(); ?>
    <?php Card::begin([
      'type' => 'card-stats bg-dark',
      'header' => 'header-icon',
      'icon' => '<i class="fas fa-user-secret"></i>',
      'color' => 'users',
      'title' => \app\models\Player::find()->active()->count(),
      'subtitle' => \Yii::t('app', 'Users'),
      'footer' => false,
    ]);
    Card::end(); ?>
    <?php Card::begin([
      'header' => 'header-icon',
      'type' => 'card-stats bg-dark',
      'icon' => '<i class="fas fa-flag"></i>',
      'color' => 'target',
      'title' => number_format($dashboardStats->claims) /*sprintf('%d / %d', $treasureStats->claimed, $treasureStats->total)*/,
      'subtitle' => \Yii::t('app', 'Claims'),
      'footer' => false,
    ]);
    Card::end(); ?>
    <?php Card::begin([
      'header' => 'header-icon',
      'type' => 'card-stats bg-dark',
      'icon' => '<i class="fas fa-chart-line"></i>',
      'color' => 'activities',
      'title' => number_format(\app\models\Stream::find()->count()),
      'subtitle' => \Yii::t('app', 'Activities'),
      'footer' => false,
    ]);
    Card::end(); ?>


  </div>
</div>