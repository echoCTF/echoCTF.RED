<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\StreamSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id) . ' / Duplicate Signup IPs';
$this->params['breadcrumbs'][] = ['label' => ucfirst(Yii::$app->controller->module->id) ];
$this->params['breadcrumbs'][] = ['label' => ucfirst(Yii::$app->controller->id), 'url'=>['index']];
$this->params['breadcrumbs'][] = ['label' => "Duplicate Signup IPs", 'url' => ['duplicate-signup-ips']];
?>
<div class="stream-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php \yii\widgets\Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
              'attribute'=>'signup_ip',
              'value'=>function($model) {return $model->signup_ip === NULL ? null : long2ip($model->signup_ip);},
              'headerOptions'=>['style'=>'width: 160px'],
            ],
            [
                'attribute'=>'duplicates',
                'contentOptions'=>['style'=>'width: 50px'],
            ],
            [
                'attribute'=>'offenders',
                'contentOptions'=>['style'=>'word-wrap:break-word'],
            ],
            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{player-view-full} {view} {update} {delete}',
              'buttons' => [
                'player-view-full' => function($url, $model) {
                  $url =  \yii\helpers\Url::to(['/frontend/profile/view-full', 'id' => $model->player->profile->id]);
                  return Html::a(
                      '<i class="bi bi-person-lines-fill"></i>',
                      $url,
                      [
                        'title' => 'View full profile',
                        'data-pjax' => '0',
                      ]
                  );
                },
              ]
            ]
        ],
    ]);?>
    <?php \yii\widgets\Pjax::end(); ?>


</div>
