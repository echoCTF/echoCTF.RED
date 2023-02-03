<?php

use yii\widgets\Pjax;
use app\widgets\Card;
use yii\widgets\ListView;
use app\widgets\target\TargetWidget;
use app\widgets\leaderboard\Leaderboard;
use app\widgets\stream\StreamWidget as Stream;

$this->_fluid = "-fluid";
$this->loadLayoutOverrides = true;
$this->title = Yii::$app->sys->event_name . ' Dashboard';
$this->_description = \Yii::t('app', "The dashboard page");
$this->registerJsFile('/js/plugins/chartist.min.js', ['depends' => 'yii\web\JqueryAsset']);
$this->registerJsFile('/js/plugins/chartist-plugin-legend.js', ['depends' => 'yii\web\JqueryAsset']);
$this->_url = \yii\helpers\Url::to([null], 'https');
?>
<div class="dashboard-index">
  <div class="body-content">
    <div class="row justify-content-center">
      <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <?php Card::begin([
          'header' => 'header-icon',
          'type' => 'card-stats',
          'icon' => '<i class="fas fa-flag"></i>',
          'color' => 'primary',
          'title' => number_format($dashboardStats->claims) /*sprintf('%d / %d', $treasureStats->claimed, $treasureStats->total)*/,
          'subtitle' => \Yii::t('app', 'Flag Claims'),
          'footer' => '<div class="stats"></div>',
        ]);
        Card::end(); ?>
      </div>
      <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <?php Card::begin([
          'header' => 'header-icon',
          'type' => 'card-stats',
          'icon' => '<i class="fas fa-chart-line"></i>',
          'color' => 'primary',
          'title' => number_format(\app\models\Stream::find()->count()),
          'subtitle' => \Yii::t('app', 'Activities'),
          'footer' => '<div class="stats"></div>',
        ]);
        Card::end(); ?>
      </div>

      <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <?php Card::begin([
          'header' => 'header-icon',
          'type' => 'card-stats',
          'icon' => '<i class="fas fa-globe"></i>',
          'color' => 'danger',
          'title' => sprintf('%d', $dashboardStats->countries),
          'subtitle' => \Yii::t('app', 'Countries'),
          'footer' => '<div class="stats"></div>',
        ]);
        Card::end(); ?>
      </div>

      <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <?php Card::begin([
          'type' => 'card-stats',
          'header' => 'header-icon',
          'icon' => '<i class="fas fa-user-secret"></i>',
          'color' => 'info',
          'title' => \app\models\Player::find()->active()->count(),
          'subtitle' => \Yii::t('app', 'Users'),
          'footer' => '<div class="stats"></div>',
        ]);
        Card::end(); ?>
      </div>
    </div>

    <div class="row">
      <?php if ($lastVisitsProvider->getModels() !== []) : ?>
        <div class="col-lg-2">
          <div class="card bg-dark">
            <div class="card-body">
              <h3 class="card-title text-center" data-toggle="tooltip" title="Last 5 targets you visited"><?= \Yii::t('app', 'Quick access') ?></h3>
              <?= ListView::widget([
                'layout' => '{items}',
                'dataProvider' => $lastVisitsProvider,
                'itemView' => '_last_visit_item',
              ]);?>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <div class="col">
        <div class="card bg-dark">
          <div class="card-body">
            <h3 class="card-title text-center"><?= \Yii::t('app', '10-Day Activity') ?></h3>
          </div>
          <div class="card-img-top ct-chart" id="LastDaysActivityChart"></div>
        </div>
      </div>
      <?php if ($newsProvider->getTotalCount() > 0) : ?>
        <div class="col-lg-4">
          <div class="card bg-dark">
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
    </div>

    <?php
    Pjax::begin(['id' => 'stream-listing', 'enablePushState' => false, 'linkSelector' => '#stream-pager a', 'formSelector' => false]);
    echo Stream::widget(['divID' => 'stream', 'dataProvider' => null, 'pagerID' => 'stream-pager']);
    Pjax::end();
    ?>

    <div class="row justify-content-center">
      <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
        <?php Card::begin([
          'type' => 'card-stats',
          'header' => 'header-icon',
          'icon' => '<i class="fas fa-level-up-alt"></i>',
          'color' => 'warning',
          'title' => \Yii::t('app', "Progress"),
          'subtitle' => \Yii::t('app', "Current level: ") . Yii::$app->user->identity->profile->experience->name,
          'footer' => '<div class="stats"></div>',
        ]);
        $x = (Yii::$app->user->identity->profile->experience->max_points - Yii::$app->user->identity->playerScore->points);
        $pct = 100 - intval(($x / Yii::$app->user->identity->profile->experience->max_points) * 100);
        ?>
        <div class="progress">
          <div class="progress-bar text-dark" role="progressbar" style="width: <?= $pct ?>%" aria-valuenow="<?= Yii::$app->user->identity->playerScore->points ?>" aria-valuemin="<?= Yii::$app->user->identity->profile->experience->min_points ?>" aria-valuemax="<?= Yii::$app->user->identity->profile->experience->max_points ?>"><b><?= $pct ?>%</b></div>
        </div>
        <?php Card::end(); ?>
      </div>

      <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
        <?php Card::begin([
          'type' => 'card-stats',
          'header' => 'header-icon',
          'icon' => '<img src="/images/headshot.svg" class="img-fluid" style="max-height: 60px;"/>',
          'color' => 'danger',
          'title' => \Yii::t('app', "Completed: ") . Yii::$app->user->identity->profile->HeadshotsCount,
          'subtitle' => \Yii::t('app', "Targets: ") . \app\modules\target\models\Target::find()->active()->count(),
          'footer' => '<div class="stats"></div>',
        ]);
        if (intval(\app\modules\target\models\Target::find()->active()->count()) != 0) {
          $headshotsPct = intval((Yii::$app->user->identity->profile->headshotsCount / intval(\app\modules\target\models\Target::find()->active()->count())) * 100);
        } else {
          $headshotsPct = 0;
        }
        ?>
        <div class="progress">
          <div class="progress-bar text-dark" role="progressbar" style="width: <?= $headshotsPct ?>%" aria-valuenow="<?= Yii::$app->user->identity->profile->headshotsCount ?>" aria-valuemin="0" aria-valuemax="<?= \app\modules\target\models\Target::find()->active()->count() ?>"><b><?= $headshotsPct ?>%</b></div>
        </div>
        <?php Card::end(); ?>
      </div>

      <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
        <?php Card::begin([
          'type' => 'card-stats',
          'header' => 'header-icon',
          'icon' => '<i class="fas fa-clipboard-list"></i>',
          'color' => 'warning',
          'title' => \Yii::t('app', "Completed: ") . Yii::$app->user->identity->profile->challengesSolverCount,
          'subtitle' => \Yii::t('app', "Challenges: ") . \app\modules\challenge\models\Challenge::find()->count(),
          'footer' => '<div class="stats"></div>',
        ]);
        if (intval(\app\modules\challenge\models\Challenge::find()->count()) > 0)
          $headshotsPct = intval((Yii::$app->user->identity->profile->challengesSolverCount / intval(\app\modules\challenge\models\Challenge::find()->count())) * 100);
        else {
          $headshotsPct = 0;
        }
        ?>
        <div class="progress">
          <div class="progress-bar text-dark" role="progressbar" style="width: <?= $headshotsPct ?>%" aria-valuenow="<?= Yii::$app->user->identity->profile->headshotsCount ?>" aria-valuemin="0" aria-valuemax="<?= \app\modules\target\models\Target::find()->active()->count() ?>"><b><?= $headshotsPct ?>%</b></div>
        </div>
        <?php Card::end(); ?>
      </div>

    </div>


  </div><!-- //body-content -->
</div>
<?php
if (!empty($dayActivity)) {
  if (intval(max($dayActivity['overallSeries'])) > 0)
    $this->registerJs("maxHigh=" . max($dayActivity['overallSeries']) . "+10;", 1);
  else
    $this->registerJs("maxHigh=20;", 1);

  $this->registerJs(
    "dataLastDaysActivityChart = {
        labels: [" . implode($dayActivity['labels'], ",") . "],
        series: [
          [" . implode($dayActivity['playerSeries'], ",") . "],
          [" . implode($dayActivity['overallSeries'], ",") . "],
        ]
      };
      LastDaysActivityChart.update(dataLastDaysActivityChart);
  ",
    4
  );
}
