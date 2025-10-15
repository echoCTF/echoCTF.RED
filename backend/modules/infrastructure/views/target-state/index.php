<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\infrastructure\models\TargetStateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Target States');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Target States'), 'url' => ['index']];
?>
<div class="target-state-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a(Yii::t('app', 'Create Target State'), ['create'], ['class' => 'btn btn-success']) ?>
  </p>

  <?php Pjax::begin(); ?>
  <?php // echo $this->render('_search', ['model' => $searchModel]);
  ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      'id',
      [
        'attribute' => 'target_name',
        'label'=>'Target',
        'value' => 'target.name'
      ],
      'total_headshots',
      'total_findings',
      'total_treasures',
      'player_rating',
      'timer_avg',
      'total_writeups',
      'approved_writeups',
      'finding_points',
      'treasure_points',
      'total_points',
      'on_network:boolean',
      'on_ondemand:boolean',
      'ondemand_state',

      [
        'class' => '\app\components\columns\ActionColumn',
        'template' => '{update} {delete} {sync}',
        'header' => Html::a(
          '<i class="fas fa-sync"></i>',
          ['sync-all'],
          [
            'title' => 'Mass sync all states',
            'data-pjax' => '0',
            'data-method' => 'POST',
            'data' => [
              'method' => 'post',
              'params' => $searchModel->attributes,
            ],

          ]
        ),
        'buttons' => [
          'sync' => function ($url, $model) {
            return Html::a('<i class="fas fa-sync"></i>', $url, [
              'class' => '',
              'title' => Yii::t('app', 'target-state-sync'),
              'data' => [
                'method' => 'post',
              ],
            ]);
          },
        ],
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>