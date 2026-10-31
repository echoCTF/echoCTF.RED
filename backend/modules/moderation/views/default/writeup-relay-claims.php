<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\moderation\models\WriteupRelayClaimSearch */
/* @var $dataProvider yii\data\SqlDataProvider */

$this->title = ucfirst(Yii::$app->controller->module->id) . ' / ' . ucfirst(Yii::$app->controller->id) . ' / Writeup Relay Claims (IP)';
$this->params['breadcrumbs'][] = ['label' => ucfirst(Yii::$app->controller->module->id)];
$this->params['breadcrumbs'][] = ['label' => ucfirst(Yii::$app->controller->id), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Writeup Relay Claims (IP)', 'url' => ['writeup-relay-claims']];

yii\bootstrap5\Modal::begin([
  'title' => '<h2><i class="bi bi-info-circle-fill"></i> ' . Html::encode($this->title) . ' Help</h2>',
  'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options' => ['class' => 'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/' . $this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="writeup-relay-claims-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <?php \yii\widgets\Pjax::begin(['id' => 'writeup-relay-claims-pjax']); ?>

  <?= $this->render('_writeup-relay-filters', ['searchModel' => $searchModel, 'action' => 'writeup-relay-claims']) ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layout' => "{summary}\n{pager}\n{items}\n{pager}",
    'columns' => [
      [
        'attribute' => 'fake',
        'format' => 'raw',
        'value' => function ($row) {
          return $this->render('_writeup-relay-player', ['id' => $row['fake_id'], 'profileId' => $row['fake_profile_id'], 'username' => $row['fake']]);
        },
      ],
      ...\app\modules\moderation\models\WriteupRelaySearch::playerColumns('fake'),
      [
        'attribute' => 'beneficiary',
        'format' => 'raw',
        'value' => function ($row) {
          return $this->render('_writeup-relay-player', ['id' => $row['beneficiary_id'], 'profileId' => $row['beneficiary_profile_id'], 'username' => $row['beneficiary']]);
        },
      ],
      ...\app\modules\moderation\models\WriteupRelaySearch::playerColumns('beneficiary'),
      [
        'attribute' => 'target',
        'format' => 'raw',
        'value' => function ($row) {
          return Html::a(Html::encode($row['target']), ['/infrastructure/target/view', 'id' => $row['target_id']], ['data-pjax' => 0]);
        },
      ],
      [
        'label' => 'Treasure',
        'value' => function ($row) {
          return $row['treasure_id'];
        },
      ],
      'activated_at',
      'claimed_at',
      [
        'attribute' => 'points',
        'label' => 'Points / base',
        'value' => function ($row) {
          return sprintf('%d / %d', $row['points'], $row['base']);
        },
      ],
    ],
  ]); ?>
  <?php \yii\widgets\Pjax::end(); ?>

</div>
