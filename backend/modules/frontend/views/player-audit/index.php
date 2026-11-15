<?php

use app\modules\frontend\models\PlayerAudit;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\modules\frontend\models\PlayerAuditSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
yii\bootstrap5\Modal::begin([
  'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
  'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();

?>
<div class="player-audit-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a(Yii::t('app', 'Truncate'), ['truncate'], [
            'class' => 'btn btn-warning',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete all the entries?'),
                'method' => 'post',
            ],
        ]) ?>
  </p>

  <?php Pjax::begin(); ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      [
        'attribute'=>'id',
        'headerOptions'=>[ 'style'=>'width: 80px'],
      ],
      [
        'attribute'=>'player_id',
        'headerOptions'=>[ 'style'=>'width: 80px'],
      ],
      ['class' => 'app\components\columns\ProfileColumn', 'attribute' => 'player_username', 'label' => Yii::t('app', 'Current Username'), 'headerOptions'=>[ 'style'=>'width: 170px'],],
      [
        'attribute'=>'field',
        'filter'=>PlayerAudit::FIELDS,
        'headerOptions'=>[ 'style'=>'width: 120px'],
      ],
      [
        'attribute'=>'value',
        'label'=>Yii::t('app', 'Change'),
        'format'=>'raw',
        'value'=>function($model) { return Html::encode($model->old_value).' <i class="fas fa-arrow-right text-warning"></i> '.Html::encode($model->new_value); },
      ],
      [
        'attribute'=>'ts',
        'headerOptions'=>[ 'style'=>'width: 170px'],
      ],
      [
        'class' => ActionColumn::class,
        'template' => '{view} {delete}',
        'urlCreator' => function ($action, PlayerAudit $model, $key, $index, $column) {
          return Url::toRoute([$action, 'id' => $model->id]);
        }
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>
