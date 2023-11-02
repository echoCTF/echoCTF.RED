<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\infrastructure\models\TargetMetadataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Targets metadata';
$this->params['breadcrumbs'][] = ['label' => 'Infrastructure', 'url' => ['/infrastructure/default/index']];
$this->params['breadcrumbs'][] = ['label' => 'Target metadata', 'url' => ['index']];
yii\bootstrap5\Modal::begin([
  'title' => '<h2><i class="bi bi-info-circle-fill"></i> ' . Html::encode($this->title) . ' Help</h2>',
  'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/index.md'), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="target-metadata-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a('Create Target metadata', ['create'], ['class' => 'btn btn-success']) ?>
  </p>

  <?php Pjax::begin(); ?>
  <?php // echo $this->render('_search', ['model' => $searchModel]);
  ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      [
        'attribute' => 'target_id',
        'headerOptions'=>['style'=>'width: 100px'],
      ],
      [
        'attribute' => 'target_name',
        'value' => 'target.name',
        'headerOptions'=>['style'=>'width: 100px'],
      ],
      [
        'attribute'=>'scenario',
        'format'=>'ntext',
        'value'=>function($model){ return substr($model->scenario,0,200); },
        'contentOptions'=>['class'=>'text-truncate','style'=>'max-width: 200px;font-size: 0.9em;'],
      ],
      [
        'attribute'=>'solution',
        'format'=>'ntext',
        'value'=>function($model){ return substr($model->solution,0,200); },
        'headerOptions'=>['style'=>'max-width: 200px;'],
        'contentOptions'=>['class'=>'text-truncate','style'=>'max-width: 200px;font-size: 0.9em;'],
      ],
      [
        'attribute'=>'pre_credits',
        'format'=>'ntext',
        'value'=>function($model){ return substr($model->pre_credits,0,200); },
        'headerOptions'=>['style'=>'max-width: 200px;'],
        'contentOptions'=>['class'=>'text-truncate','style'=>'max-width: 200px;font-size: 0.9em;'],
      ],
      //'post_credits:ntext',
      //'pre_exploitation:ntext',
      //'post_exploitation:ntext',
      //'created_at',
      //'updated_at',

      ['class' => 'yii\grid\ActionColumn'],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>