<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use app\modules\moderation\models\HeadshotClusterSearch;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\moderation\models\HeadshotClusterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = ucfirst(Yii::$app->controller->module->id) . ' / ' . ucfirst(Yii::$app->controller->id) . ' / Headshot Clusters';
$this->params['breadcrumbs'][] = ['label' => ucfirst(Yii::$app->controller->module->id)];
$this->params['breadcrumbs'][] = ['label' => ucfirst(Yii::$app->controller->id), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Headshot Clusters', 'url' => ['headshot-clusters']];

yii\bootstrap5\Modal::begin([
  'title' => '<h2><i class="bi bi-info-circle-fill"></i> ' . Html::encode($this->title) . ' Help</h2>',
  'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options' => ['class' => 'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/' . $this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();

$mode = $searchModel->mode;
$fmt = Yii::$app->formatter;
?>
<div class="headshot-clusters-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <?php \yii\widgets\Pjax::begin(['id' => 'headshot-clusters-pjax']); ?>

  <div class="card mb-3">
    <div class="card-body">
      <?php $form = ActiveForm::begin([
        'action' => ['headshot-clusters'],
        'method' => 'get',
        'options' => ['data-pjax' => 1],
      ]); ?>
      <div class="row">
        <div class="col-md-2"><?= $form->field($searchModel, 'mode')->dropDownList(HeadshotClusterSearch::modes()) ?></div>
        <div class="col-md-2"><?= $form->field($searchModel, 'min_headshots')->input('number', ['min' => 2]) ?></div>
        <div class="col-md-2"><?= $form->field($searchModel, 'gap')->input('number', ['min' => 1])->hint('Burst mode only') ?></div>
        <div class="col-md-2"><?= $form->field($searchModel, 'fast')->input('number', ['min' => 0]) ?></div>
        <div class="col-md-2"><?= $form->field($searchModel, 'date_from')->input('date') ?></div>
        <div class="col-md-2"><?= $form->field($searchModel, 'date_to')->input('date') ?></div>
      </div>
      <div class="row">
        <div class="col-md-3"><?= $form->field($searchModel, 'target') ?></div>
        <div class="col-md-3"><?= $form->field($searchModel, 'username') ?></div>
        <div class="col-md-3"><?= $form->field($searchModel, 'skip_launch_days')->input('number', ['min' => 0]) ?></div>
        <div class="col-md-3 d-flex align-items-center">
          <?= Html::submitButton('Search', ['class' => 'btn btn-primary me-2']) ?>
          <?= Html::a('Reset', ['headshot-clusters'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
      </div>
      <?php ActiveForm::end(); ?>
    </div>
  </div>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "{summary}\n{pager}\n{items}\n{pager}",
    'rowOptions' => function ($row) {
      $headshots = (int) $row['headshots'];
      $fast = (int) $row['fast_solves'];
      if ((int) $row['ip_overlap'] > 0 || (int) $row['vpn_overlap'] > 0 || ($headshots > 0 && $fast * 2 >= $headshots && $fast > 1)) {
        return ['class' => 'table-danger'];
      }
      if ($fast > 0 || (int) $row['team_overlap'] > 0) {
        return ['class' => 'table-warning'];
      }
      return [];
    },
    'columns' => [
      [
        'attribute' => 'target_name',
        'label' => 'Target',
        'format' => 'raw',
        'value' => function ($row) {
          return Html::a(Html::encode($row['target_name']), ['/infrastructure/target/view', 'id' => $row['target_id']])
            . '<br><small class="text-muted">' . Html::encode($row['target_ip']) . '</small>';
        },
      ],
      [
        'attribute' => 'first_at',
        'label' => $mode === HeadshotClusterSearch::MODE_BURST ? 'Burst' : ucfirst($mode),
        'format' => 'raw',
        'value' => function ($row) use ($mode, $fmt) {
          $label = $mode === HeadshotClusterSearch::MODE_WEEK ? 'Week of ' . $row['bucket'] : ($mode === HeadshotClusterSearch::MODE_DAY ? $row['bucket'] : $row['first_at']);
          return Html::encode($label)
            . '<br><small class="text-muted">' . Html::encode($row['first_at']) . ' &rarr; ' . Html::encode($row['last_at']) . '</small>';
        },
      ],
      [
        'attribute' => 'headshots',
        'format' => 'raw',
        'value' => function ($row) use ($mode) {
          $params = ['/activity/headshot/index', 'HeadshotSearch[target_id]' => $row['target_id']];
          if ($mode === HeadshotClusterSearch::MODE_DAY) {
            $params['HeadshotSearch[created_at]'] = $row['bucket'];
          }
          return Html::a((int) $row['headshots'], $params, ['data-pjax' => 0, 'title' => 'Open in headshots activity']);
        },
      ],
      [
        'attribute' => 'span_seconds',
        'label' => 'Span',
        'value' => function ($row) use ($fmt) {
          return (int) $row['span_seconds'] > 0 ? $fmt->asDuration((int) $row['span_seconds']) : '0s';
        },
      ],
      [
        'attribute' => 'avg_timer',
        'label' => 'Timer avg / min',
        'format' => 'raw',
        'value' => function ($row) use ($fmt) {
          return Html::tag('abbr', (int) $row['avg_timer'], ['title' => $fmt->asDuration((int) $row['avg_timer'])])
            . ' / ' . Html::tag('abbr', (int) $row['min_timer'], ['title' => $fmt->asDuration((int) $row['min_timer'])]);
        },
      ],
      [
        'attribute' => 'fast_solves',
        'label' => 'Fast',
        'headerOptions' => ['title' => 'Headshots with a timer below the fast solve threshold'],
      ],
      [
        'attribute' => 'ip_overlap',
        'label' => 'IP / VPN / Team overlap',
        'headerOptions' => ['title' => 'Players beyond the first sharing a last signin IP / last VPN remote IP / team'],
        'value' => function ($row) {
          return sprintf('%d / %d / %d', $row['ip_overlap'], $row['vpn_overlap'], $row['team_overlap']);
        },
      ],
      [
        'attribute' => 'share',
        'label' => 'Share',
        'headerOptions' => ['title' => 'Percentage of all the target headshots that fall in this cluster'],
        'value' => function ($row) {
          return $row['share'] === null ? null : sprintf('%s%% of %d', $row['share'], $row['target_total']);
        },
      ],
      [
        'attribute' => 'days_since_release',
        'label' => 'Target age',
        'value' => function ($row) {
          return $row['days_since_release'] === null ? null : sprintf('%dd', $row['days_since_release']);
        },
      ],
      [
        'label' => 'Players (in solve order)',
        'format' => 'raw',
        'contentOptions' => ['style' => 'word-wrap:break-word; max-width: 420px'],
        'value' => function ($row) {
          $links = [];
          foreach (HeadshotClusterSearch::parsePlayers($row['players']) as $id => $username) {
            $links[] = Html::a(Html::encode($username), ['/frontend/player/view', 'id' => $id], ['data-pjax' => 0]);
          }
          return implode(', ', $links);
        },
      ],
    ],
  ]); ?>
  <?php \yii\widgets\Pjax::end(); ?>

</div>
