<?php

use app\modules\moderation\models\SharedNetwork;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\modules\moderation\models\SharedNetworkSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Shared Networks');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Shared Networks'), 'url' => ['index']];
?>
<div class="shared-network-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a(Yii::t('app', 'Create Shared Network'), ['create'], ['class' => 'btn btn-success']) ?>
  </p>

  <?php Pjax::begin(); ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      [
        'attribute' => 'id',
        'headerOptions' => ['style' => 'width:4em'],
      ],
      [
        'attribute' => 'cidr',
        'headerOptions' => ['style' => 'width:12em'],
        'value' => function (SharedNetwork $model) {
          return $model->cidr;
        },
      ],
      'description',
      'created_at',
      ['class' => ActionColumn::class],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>
