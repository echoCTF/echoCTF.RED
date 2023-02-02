<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\PlayerLastSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=Yii::t('app', 'Players Last activity');
$this->params['breadcrumbs'][]=$this->title;
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap\Modal::end();
?>
<div class="player-last-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
              'attribute'=>'username',
              'value'=>'player.username'
            ],
            [
              'attribute'=>'on_pui',
              'value'=>function($model) { return $model->on_pui == 0 ? null : $model->on_pui;}
            ],
            [
              'attribute'=>'on_vpn',
              'value'=>function($model) {return $model->on_vpn == 0 ? null : $model->on_vpn;}
            ],
            [
              'attribute'=>'vpn_remote_address',
              'value'=>function($model) {return $model->vpn_remote_address === NULL ? null : long2ip($model->vpn_remote_address);},
            ],
            [
              'attribute'=>'vpn_local_address',
              'value'=>function($model) {return $model->vpn_local_address === null ? null : long2ip($model->vpn_local_address);},
            ],
            [
              'attribute'=>'signup_ip',
              'value'=>function($model) {return $model->signup_ip === NULL ? null : long2ip($model->signup_ip);},
            ],
            [
              'attribute'=>'signin_ip',
              'value'=>function($model) {return $model->signin_ip === NULL ? null : long2ip($model->signin_ip);},
            ],
            'ts',
            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{player-view-full} {view} {update} {delete}',
              'buttons' => [
                'player-view-full' => function($url, $model) {
                  $url =  \yii\helpers\Url::to(['/frontend/profile/view-full', 'id' => $model->player->profile->id]);
                  return Html::a(
                      '<span class="glyphicon glyphicon-user"></span>',
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


</div>
