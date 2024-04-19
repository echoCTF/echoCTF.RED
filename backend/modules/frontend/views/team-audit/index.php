<?php

use app\modules\frontend\models\TeamAudit;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\modules\frontend\models\TeamAuditSearch $searchModel */
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
<div class="team-audit-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a(Yii::t('app', 'Create Team Audit'), ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a(Yii::t('app', 'Truncate'), ['truncate'], [
            'class' => 'btn btn-warning',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete all the entries?'),
                'method' => 'post',
            ],
        ]) ?>
  </p>

  <?php Pjax::begin(); ?>
  <?php // echo $this->render('_search', ['model' => $searchModel]);
  ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      [
        'attribute'=>'id',
        'headerOptions'=>[ 'style'=>'width: 80px'],
      ],
      [
        'attribute'=>'team_id',
        'headerOptions'=>[ 'style'=>'width: 80px'],
      ],

      [
        'attribute' => 'team_name',
        'value' => 'team.name',
        'headerOptions'=>[ 'style'=>'width: 170px'],
      ],
      ['class' => 'app\components\columns\ProfileColumn', 'idkey' => 'player.id', 'attribute' => 'player_username', 'field' => 'player.username', 'headerOptions'=>[ 'style'=>'width: 170px'],],
      [
        'attribute'=>'action',
        'headerOptions'=>[ 'style'=>'width: 120px'],
      ],

      'message:ntext',
      [
        'attribute'=>'ts',
        'headerOptions'=>[ 'style'=>'width: 150px'],
      ],
      [
        'class' => ActionColumn::class,
        'urlCreator' => function ($action, TeamAudit $model, $key, $index, $column) {
          return Url::toRoute([$action, 'id' => $model->id]);
        }
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>