<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\frontend\models\PlayerSpinSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=Yii::t('app', 'Player Spins');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="player-spin-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Spin'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Reset all spins'), ['reset'], ['class' => 'btn btn-info',
          'data' => [
              'confirm' => Yii::t('app', 'Are you sure you want to reset all counters?'),
              'method' => 'post',
          ],
        ]) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'player_id',
            ['class' => 'app\components\columns\ProfileColumn','attribute'=>'player'],
            'counter',
            'perday',
            'total',
            'updated_at:date',
            'ts',

            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{reset} {view} {update} {delete}',
              'buttons' => [
                  'reset' => function($url) {
                      return Html::a(
                          '<i class="bi bi-arrow-clockwise"></i>',
                          $url,
                          [
                              'title' => 'Reset Counters',
                              'data-pjax' => '0',
                              'data-method' => 'POST',
                          ]
                      );
                  },
              ],
            ],
        ],
    ]);?>


</div>
