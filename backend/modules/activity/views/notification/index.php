<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\NotificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Notifications';
$this->params['breadcrumbs'][] = ['label' => 'Notifications', 'url' => ['index']];
yii\bootstrap5\Modal::begin([
  'title' => '<h2><i class="bi bi-info-circle-fill"></i> ' . Html::encode($this->title) . ' Help</h2>',
  'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/' . $this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="notification-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a('Create', ['create'], ['class' => 'btn btn-success', 'data-toggle' => 'tooltip', 'title' => 'Create a new notification']) ?>
    <?= Html::a('Rotate', ['rotate'], [
      'class' => 'btn btn-warning',
      'data-toggle' => 'tooltip',
      'title' => 'Rotate old notifications',
      'data' => [
        'confirm' => Yii::t('app', 'This clears archived and pending notifications using the default settings of 180 minutes for the archived and 3 days for pending notifications about target operations.'),
      ],
    ]) ?>
  </p>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'rowOptions'=>['class'=>'align-middle'],
    'columns' => [

      'id',
      ['class' => 'app\components\columns\ProfileColumn', 'attribute' => 'player'],
      'category',
      'title',
      [
        'class' => 'app\components\columns\BooleanColumn',
        'attribute' => 'archived',
        'contentOptions'=>['class'=>'text-center fs-5'],
        'filter' => ['1' => 'Archived ', '0' => 'Pending ']
      ],
      [
        'attribute' => 'created_at',
        'format'=>'raw',
        'value'=>function($model){return Html::tag('abbr',Yii::$app->formatter->asRelativeTime($model->created_at),['title'=>$model->created_at]);}
      ],
      //'updated_at',

      ['class' => 'yii\grid\ActionColumn'],
    ],
  ]); ?>


</div>