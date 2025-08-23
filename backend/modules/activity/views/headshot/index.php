<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\HeadshotSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Headshots');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
yii\bootstrap5\Modal::begin([
  'title' => '<h2><i class="bi bi-info-circle-fill"></i> ' . Html::encode($this->title) . ' Help</h2>',
  'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options' => ['class' => 'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/index.md'), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="headshot-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a(Yii::t('app', 'Create Headshot'), ['create'], ['class' => 'btn btn-success']) ?>
  </p>

  <?php Pjax::begin(); ?>
  <?php // echo $this->render('_search', ['model' => $searchModel]);
  ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      ['class' => 'yii\grid\SerialColumn'],

      'player_id',
      ['class' => 'app\components\columns\ProfileColumn'],
      'target_id',
      [
        'attribute' => 'name',
        'value' => 'target.name',
      ],
      'timer',
      'first',
      [
        'attribute' => 'rating',
        'filter' => $searchModel->ratings,
        'value' => 'ratingString'
      ],
      'created_at',

      [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view} {update} {delete} {zero}',
        'header' => Html::a(
          '<i class="fab fa-creative-commons-zero"></i>',
          ['/activity/headshot/zero-filtered'],
          [
            'title' => 'Zero out filtered headshots',
            'data-pjax' => '0',
            'data-method' => 'POST',
            'data' => [
              'method' => 'post',
              'params' => $searchModel->attributes,
              'confirm' => 'Are you sure you want to zero out the filtered headshots?',
            ],
          ]
        ),
        'buttons' => [
          'zero' => function ($url, $model) {
            return Html::a('<i class="fab fa-creative-commons-zero"></i>', ['/activity/headshot/zero', 'player_id' => $model->player_id, 'target_id' => $model->target_id], [
              'class' => '',
              'title' => 'Zero out headshot and points',
              'data' => [
                'confirm' => 'This operation zeroes out the current headshot timer and points for the player, it also activates the writeup for this player & target (if any). Are you absolutely sure you absolutely sure about this?',
                'method' => 'post',
              ],
            ]);
          },
        ]
      ],
      // zero timer and points
      // add writeup for headshot <i class="fas fa-cookie-bite"></i>
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>