<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\moderation\models\FastSolveSearch */
/* @var $dataProvider yii\data\SqlDataProvider */

$this->title = ucfirst(Yii::$app->controller->module->id) . ' / ' . ucfirst(Yii::$app->controller->id) . ' / Fast Solves';
$this->params['breadcrumbs'][] = ['label' => ucfirst(Yii::$app->controller->module->id)];
$this->params['breadcrumbs'][] = ['label' => ucfirst(Yii::$app->controller->id), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Fast Solves', 'url' => ['fast-solves']];

yii\bootstrap5\Modal::begin([
  'title' => '<h2><i class="bi bi-info-circle-fill"></i> ' . Html::encode($this->title) . ' Help</h2>',
  'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options' => ['class' => 'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/' . $this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();

$fmt = Yii::$app->formatter;
// seconds as a short duration, eg 3h 12m
$duration = function ($seconds) use ($fmt) {
  $seconds = (int) round((float) $seconds);
  return $seconds > 0 ? $fmt->asDuration($seconds) : '0s';
};
?>
<div class="fast-solves-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <?php \yii\widgets\Pjax::begin(['id' => 'fast-solves-pjax']); ?>

  <div class="card mb-3">
    <div class="card-body">
      <?php $form = ActiveForm::begin([
        'action' => ['fast-solves'],
        'method' => 'get',
        'options' => ['data-pjax' => 1],
      ]); ?>
      <div class="row">
        <div class="col-md-2"><?= $form->field($searchModel, 'username') ?></div>
        <div class="col-md-2"><?= $form->field($searchModel, 'target') ?></div>
        <div class="col-md-2"><?= $form->field($searchModel, 'since')->input('date') ?></div>
        <div class="col-md-2"><?= $form->field($searchModel, 'writeups')->dropDownList(['0' => 'Without writeups', '1' => 'With writeups'], ['prompt' => 'Any']) ?></div>
        <div class="col-md-2"><?= $form->field($searchModel, 'player_status')->dropDownList(\app\modules\frontend\models\Player::STATUSES, ['prompt' => 'Any']) ?></div>
        <?php if (intval(Yii::$app->sys->academic_grouping) > 0) :
          $academics = [];
          for ($i = 0; $i < intval(Yii::$app->sys->academic_grouping); $i++) {
            $academics[$i] = Yii::$app->sys->{'academic_' . $i . 'short'};
          } ?>
          <div class="col-md-2"><?= $form->field($searchModel, 'player_academic')->dropDownList($academics, ['prompt' => 'Any']) ?></div>
        <?php endif; ?>
      </div>
      <div class="row">
        <div class="col-md-2"><?= $form->field($searchModel, 'max_ratio')->input('number', ['min' => 0, 'max' => 1, 'step' => '0.01'])->hint('0.1 = 10x faster than the median') ?></div>
        <div class="col-md-2"><?= $form->field($searchModel, 'min_target_solves')->input('number', ['min' => 2]) ?></div>
        <div class="col-md-2"><?= $form->field($searchModel, 'usual_ratio')->input('number', ['min' => 0, 'step' => '0.05']) ?></div>
        <div class="col-md-2"><?= $form->field($searchModel, 'new_player_solves')->input('number', ['min' => 0]) ?></div>
        <div class="col-md-2"><?= $form->field($searchModel, 'min_bytes')->input('number', ['min' => 0]) ?></div>
        <div class="col-md-2 d-flex align-items-center">
          <?= Html::submitButton('Search', ['class' => 'btn btn-primary me-2']) ?>
          <?= Html::a('Reset', ['fast-solves'], ['class' => 'btn btn-outline-secondary']) ?>
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
      // fast, little traffic and no writeups is the strongest case
      if ((int) $row['strong'] === 1) {
        return ['class' => 'table-danger'];
      }
      return (int) $row['had_writeups'] === 1 ? [] : ['class' => 'table-warning'];
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
          return Html::a(Html::encode($row['target']), ['/infrastructure/target/view', 'id' => $row['target_id']], ['data-pjax' => 0])
            . '<br><small class="text-muted">' . (int) $row['target_solves'] . ' solves</small>';
        },
      ],
      'solved_at',
      [
        'attribute' => 'timer',
        'label' => 'Timer / median',
        'format' => 'raw',
        'value' => function ($row) use ($duration) {
          return Html::tag('abbr', Html::encode($duration($row['timer'])), ['title' => (int) $row['timer'] . ' seconds'])
            . ' / ' . Html::encode($duration($row['target_median']));
        },
      ],
      [
        'attribute' => 'ratio',
        'label' => 'Faster',
        'headerOptions' => ['title' => 'How many times faster than the target median'],
        'value' => function ($row) {
          $ratio = (float) $row['ratio'];
          return $ratio > 0 ? sprintf('%sx', number_format(1 / $ratio, 1)) : 'instant';
        },
      ],
      [
        'attribute' => 'player_usual_ratio',
        'label' => 'Usual pace',
        'headerOptions' => ['title' => "The player's median ratio over their judged solves, 1 = typical, 0.5 = twice as fast"],
        'value' => function ($row) {
          return sprintf('%s (%d solves)', number_format((float) $row['player_usual_ratio'], 2), $row['player_solves']);
        },
      ],
      [
        'attribute' => 'bytes',
        'label' => 'Traffic',
        'format' => 'raw',
        'value' => function ($row) {
          if ((int) $row['has_traffic'] === 0) {
            return Html::tag('span', 'none', ['class' => 'text-danger']);
          }
          return Yii::$app->formatter->asShortSize((int) $row['bytes']);
        },
      ],
      [
        'label' => 'Writeups',
        'value' => function ($row) {
          return (int) $row['had_writeups'] === 1 ? 'yes' : 'no';
        },
      ],
    ],
  ]); ?>
  <?php \yii\widgets\Pjax::end(); ?>

</div>
