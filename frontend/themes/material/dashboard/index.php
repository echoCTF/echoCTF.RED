<?php

use yii\widgets\Pjax;
use app\widgets\Card;
use yii\widgets\ListView;
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

  <?php echo $this->render('_top', ['dashboardStats' => $dashboardStats, 'newsProvider' => $newsProvider,'lastVisitsProvider'=>$lastVisitsProvider,'active_targets'=>$active_targets]); ?>
<?php if(!empty($dayActivity)):?>
    <div class="row justify-content-center">
      <div class="col">
        <div class="card bg-dark">
          <div class="card-body">
            <h3 class="card-title text-center"><?= \Yii::t('app', '10-Day Activity') ?></h3>
            <div class="card-img-top ct-chart" id="LastDaysActivityChart"></div>
          </div>
        </div>
      </div>
    </div>
<?php endif;?>
    <?php
    Pjax::begin(['id' => 'stream-listing', 'enablePushState' => false, 'linkSelector' => '#stream-pager a', 'formSelector' => false]);
    echo Stream::widget(['divID' => 'stream', 'dataProvider' => null, 'pagerID' => 'stream-pager']);
    Pjax::end();
    ?>
    <div class="row justify-content-center">
      <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
        <?php Card::begin([
          'type' => 'card-stats bg-dark',
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
          'type' => 'card-stats bg-dark',
          'header' => 'header-icon',
          'icon' => '<img src="/images/headshot.svg" class="img-fluid" style="max-height: 60px;"/>',
          'color' => 'danger',
          'title' => \Yii::t('app', "Completed: ") . Yii::$app->user->identity->profile->HeadshotsCount,
          'subtitle' => \Yii::t('app', "Targets: ") . $active_targets,
          'footer' => '<div class="stats"></div>',
        ]);
        if (intval($active_targets) != 0) {
          $headshotsPct = intval((Yii::$app->user->identity->profile->headshotsCount / intval($active_targets)) * 100);
        } else {
          $headshotsPct = 0;
        }
        ?>
        <div class="progress">
          <div class="progress-bar text-dark" role="progressbar" style="width: <?= $headshotsPct ?>%" aria-valuenow="<?= Yii::$app->user->identity->profile->headshotsCount ?>" aria-valuemin="0" aria-valuemax="<?= $active_targets ?>"><b><?= $headshotsPct ?>%</b></div>
        </div>
        <?php Card::end(); ?>
      </div>

      <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
        <?php Card::begin([
          'type' => 'card-stats bg-dark',
          'header' => 'header-icon',
          'icon' => '<i class="fas fa-clipboard-list"></i>',
          'color' => 'warning',
          'title' => \Yii::t('app', "Completed: ") . Yii::$app->user->identity->profile->challengesSolverCount,
          'subtitle' => \Yii::t('app', "Challenges: ") . $active_challenges,
          'footer' => '<div class="stats"></div>',
        ]);
        if (intval($active_challenges) > 0)
          $headshotsPct = intval((Yii::$app->user->identity->profile->challengesSolverCount / intval($active_challenges)) * 100);
        else {
          $headshotsPct = 0;
        }
        ?>
        <div class="progress">
          <div class="progress-bar text-dark" role="progressbar" style="width: <?= $headshotsPct ?>%" aria-valuenow="<?= Yii::$app->user->identity->profile->headshotsCount ?>" aria-valuemin="0" aria-valuemax="<?= $active_targets ?>"><b><?= $headshotsPct ?>%</b></div>
        </div>
        <?php Card::end(); ?>
      </div>
    </div>
  </div><!-- //body-content -->
</div>
<?php
if (!empty($dayActivity)) {
  if (intval(max($dayActivity['overallSeries'])) > 0)
    $this->registerJs("maxHigh=" . (max($dayActivity['overallSeries']) + 10) . ";", 1);
  else
    $this->registerJs("maxHigh=20;", 1);

  $this->registerJs(
    "dataLastDaysActivityChart = {
        labels: [" . implode(",",$dayActivity['labels']) . "],
        series: [
          [" . implode(",",$dayActivity['playerSeries']) . "],
          [" . implode(",",$dayActivity['overallSeries']) . "],
        ]
      };
      LastDaysActivityChart.update(dataLastDaysActivityChart);
  ",
    4
  );
}
