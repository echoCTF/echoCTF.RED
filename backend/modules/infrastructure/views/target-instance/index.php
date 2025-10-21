<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\modules\infrastructure\models\Server;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\infrastructure\models\TargetInstanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Target Instances');
$this->params['breadcrumbs'][] = ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
yii\bootstrap5\Modal::begin([
  'title' => '<h2><i class="bi bi-info-circle-fill"></i> ' . Html::encode($this->title) . ' Help</h2>',
  'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options' => ['class' => 'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/index.md'), 'gfm');
yii\bootstrap5\Modal::end();

?>
<div class="target-instance-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a(Yii::t('app', 'Create Target Instance'), ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a(Yii::t('app', 'Destroy Instances'), ['destroy-all'], [
      'class' => 'btn btn-danger',
      'title'=>'Destroy all instances',
      'data-confirm' => 'Are you sure you want to destroy all instances?',
      'data-pjax' => '0',
      'data-method' => 'POST',
    ]) ?>
  </p>

  <?php Pjax::begin(); ?>
  <?php // echo $this->render('_search', ['model' => $searchModel]);
  ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      'name',
      ['class' => 'app\components\columns\ProfileColumn'],
      [
        'attribute' => 'targetname',
        'label' => 'Target',
        'value' => function ($model) {
          return $model->target_id . ': ' . $model->target->name;
        }
      ],
      [
        'attribute' => 'server_id',
        'filter' => ArrayHelper::map(Server::find()->orderBy(['name' => SORT_ASC, 'ip' => SORT_ASC])->asArray()->all(), 'id', 'name'),
        'value' => function ($model) {
          if ($model->server) return $model->server_id . ': ' . $model->server->name;
          return null;
        }
      ],
      'ipoctet',
      [
        'attribute' => 'reboot',
        'value' => 'rebootVal',
        'filter' => [0 => 'Start / Do Nothing', 1 => 'Restart', 2 => 'Destroy'],
        'headerOptions' => ['style' => 'width:7em'],
        'contentOptions' => ['style' => 'white-space: nowrap;'],
      ],
      'team_allowed:boolean',
      [
        'attribute' => 'created_at',
        'contentOptions' => ['style' => 'white-space: nowrap;'],
      ],
      [
        'attribute' => 'updated_at',
        'contentOptions' => ['style' => 'white-space: nowrap;'],
      ],
      [
        'class' => '\app\components\columns\ActionColumn',
        'notifyIdValue' => function ($model) {
          return $model->player_id;
        },
        'template' => '{restart} {destroy} {view} {update} {delete} {notify}',
        'urlCreator' => function ($action, $model, $key, $index, $column) {
          if ($action === 'notify') {
            return \yii\helpers\Url::to(['/frontend/player/notify', 'id' => $model->player_id]);
          }
          return \yii\helpers\Url::to([$action, 'id' => $model->player_id]);
        },

        'buttons' => [
          'restart' => function ($url) {
            return Html::a(
              '<i class="bi bi-arrow-clockwise"></i>',
              $url,
              [
                'title' => 'Restart instance',
                'data-pjax' => '0',
                'data-confirm' => 'You are about to start/restart this instance. Are you sure?',
                'data-method' => 'POST',
              ]
            );
          },
          'destroy' => function ($url) {
            return Html::a(
              '<i class="bi bi-power"></i>',
              $url,
              [
                'title' => 'Destroy container',
                'data-pjax' => '0',
                'data-confirm' => 'You are about to destroy this instance. Are you sure?',
                'data-method' => 'POST',
              ]
            );
          },
        ],
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>