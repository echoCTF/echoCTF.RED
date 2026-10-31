<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\moderation\models\ClaimsWithoutTrafficSearch */
/* @var $dataProvider yii\data\SqlDataProvider */

$this->title = ucfirst(Yii::$app->controller->module->id) . ' / ' . ucfirst(Yii::$app->controller->id) . ' / Claims Without Traffic';
$this->params['breadcrumbs'][] = ['label' => ucfirst(Yii::$app->controller->module->id)];
$this->params['breadcrumbs'][] = ['label' => ucfirst(Yii::$app->controller->id), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Claims Without Traffic', 'url' => ['claims-without-traffic']];

yii\bootstrap5\Modal::begin([
  'title' => '<h2><i class="bi bi-info-circle-fill"></i> ' . Html::encode($this->title) . ' Help</h2>',
  'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options' => ['class' => 'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/' . $this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();

$fmt = Yii::$app->formatter;
?>
<div class="claims-without-traffic-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <?php \yii\widgets\Pjax::begin(['id' => 'claims-without-traffic-pjax']); ?>

  <div class="card mb-3">
    <div class="card-body">
      <?php $form = ActiveForm::begin([
        'action' => ['claims-without-traffic'],
        'method' => 'get',
        'options' => ['data-pjax' => 1],
      ]); ?>
      <div class="row">
        <div class="col-md-2"><?= $form->field($searchModel, 'username') ?></div>
        <div class="col-md-2"><?= $form->field($searchModel, 'target') ?></div>
        <div class="col-md-2"><?= $form->field($searchModel, 'since')->input('date')->hint('Never earlier than the first traffic record') ?></div>
        <div class="col-md-2"><?= $form->field($searchModel, 'min_bytes')->input('number', ['min' => 0]) ?></div>
        <div class="col-md-2"><?= $form->field($searchModel, 'player_status')->dropDownList(\app\modules\frontend\models\Player::STATUSES, ['prompt' => 'Any']) ?></div>
        <?php if (intval(Yii::$app->sys->academic_grouping) > 0) :
          $academics = [];
          for ($i = 0; $i < intval(Yii::$app->sys->academic_grouping); $i++) {
            $academics[$i] = Yii::$app->sys->{'academic_' . $i . 'short'};
          } ?>
          <div class="col-md-2"><?= $form->field($searchModel, 'player_academic')->dropDownList($academics, ['prompt' => 'Any']) ?></div>
        <?php endif; ?>
        <div class="col-md-2 d-flex align-items-center">
          <?= Html::submitButton('Search', ['class' => 'btn btn-primary me-2']) ?>
          <?= Html::a('Reset', ['claims-without-traffic'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
      </div>
      <?php ActiveForm::end(); ?>
    </div>
  </div>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layout' => "{summary}\n{pager}\n{items}\n{pager}",
    'rowOptions' => function ($row) {
      // no traffic at all, or claiming before ever touching the target, is the strongest case
      $signals = (string) $row['signals'];
      if (strpos($signals, 'no_traffic') !== false || strpos($signals, 'claimed_before_traffic') !== false) {
        return ['class' => 'table-danger'];
      }
      return ['class' => 'table-warning'];
    },
    'columns' => [
      [
        'attribute' => 'username',
        'label' => 'Player',
        'format' => 'raw',
        'value' => function ($row) {
          return $this->render('_writeup-relay-player', ['id' => $row['player_id'], 'profileId' => $row['player_profile_id'], 'username' => $row['username']]);
        },
      ],
      ...\app\modules\moderation\models\WriteupRelaySearch::playerColumns('player'),
      [
        'attribute' => 'target',
        'format' => 'raw',
        'value' => function ($row) {
          return Html::a(Html::encode($row['target']), ['/infrastructure/target/view', 'id' => $row['target_id']], ['data-pjax' => 0]);
        },
      ],
      'claims',
      'claim_points',
      'first_claim',
      [
        'attribute' => 'first_packet',
        'value' => function ($row) {
          return $row['first_packet'] ?? 'never';
        },
      ],
      [
        'attribute' => 'bytes',
        'label' => 'Traffic',
        'format' => 'raw',
        'value' => function ($row) use ($fmt) {
          return Html::tag('abbr', $fmt->asShortSize((int) $row['bytes']), ['title' => sprintf('%d bytes, %d packets', $row['bytes'], $row['packets'])]);
        },
      ],
      [
        'attribute' => 'signals',
        'format' => 'raw',
        'value' => function ($row) {
          $tags = [];
          foreach (preg_split('/\s+/', trim((string) $row['signals'])) as $signal) {
            if ($signal !== '') {
              $tags[] = Html::tag('code', Html::encode($signal));
            }
          }
          return implode(' ', $tags);
        },
      ],
    ],
  ]); ?>
  <?php \yii\widgets\Pjax::end(); ?>

</div>
