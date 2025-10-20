<?php

use app\modules\moderation\models\Abuser;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\modules\frontend\models\AbuserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Abusers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="abuser-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a(Yii::t('app', 'Create Abuser'), ['create'], ['class' => 'btn btn-success']) ?>
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
      ['class' => 'app\components\columns\ProfileColumn', 'idkey' => 'player.profile.id', 'attribute' => 'username', 'field' => 'player.username'],
      [
        'attribute' => 'title',
        'headerOptions' => ['style' => 'width:18em'],
        'format'=>'html',
        'value'=>function($model) { if (trim($model->body)!='') return $model->body; return $model->title;}
      ],
//      'reason',
      'model',
      'model_id',
      'created_at',
      'updated_at',
      [
        'class' => ActionColumn::class,
        'template' => ' {view} {update} {delete} {analyze}',
        //'urlCreator' => function ($action, Abuser $model, $key, $index, $column) {
        //  return Url::toRoute([$action, 'id' => $model->id]);
        //}
        //failed_claim
        //
//        'visibleButtons' => [
//          'analysis' => function ($model) {
//            if ($model->last->vpn_local_address !== null) return true;
//            return false;
//          },
//        ],
        'buttons' => [
          'analyze' => function ($url, $model, $key) {
            return Html::a(
              '<i class="fas fa-diagnoses"></i>',
              Url::to(['analyze', 'id' => $model->id]),
              [
                'title' => 'Perform analysis on record',
                'data-pjax' => '0',
              ]
            );
          },
        ],
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>