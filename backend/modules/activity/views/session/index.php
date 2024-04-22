<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\SessionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=Yii::t('app', 'Sessions');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="sessions-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Delete expired'), ['delete-expired'], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete all expired sessions?'),
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
            'ipoctet',
            'id',
            'expire:dateTime',
            'ts:dateTime',

            [
              'class' => 'yii\grid\ActionColumn',
              'header' => Html::a(
                  '<i class="bi bi-trash"></i>',
                  ['delete-filtered'],
                  [
                      'title' => 'Mass Delete sessions',
                      'data-pjax' => '0',
                      'data-method' => 'POST',
                      'data'=>[
                        'method'=>'post',
                        'params'=> $searchModel->attributes,
                        'confirm'=>'Are you sure you want to delete the currently filtered sessions?',
                      ],
                  ]
              ),
            ],
        ],
    ]);?>


</div>
