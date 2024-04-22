<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\modules\infrastructure\models\Server;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\infrastructure\models\TargetInstanceAuditSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Target Instance Audits');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/index.md'), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="target-instance-audit-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Yii::t('app', 'Delete all records'), ['truncate'], ['class' => 'btn btn-danger',                          'data-pjax' => '0',
                          'data-confirm'=>'Are you sure you want to delete all the audit records?',
                          'data-method' => 'POST',
]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
               'attribute' => 'op',
               'headerOptions' => ['style' => 'width:4em'],
            ],
            [
                'attribute'=>'player_id',
                'value'=>function($model){return $model->player_id.': '.$model->player->username;}
            ],
            [
                'attribute'=>'target_id',
                'value'=>function($model){return $model->target_id.': '.$model->target->name;}
            ],
            [
                'attribute'=>'server_id',
                'filter'=>ArrayHelper::map(Server::find()->orderBy(['name'=>SORT_ASC,'ip'=>SORT_ASC])->asArray()->all(),'id','name'),
                'value'=>function($model){return $model->server_id.': '.$model->server->name;}
            ],
            'ipoctet',
            [
                'attribute'=>'reboot',
                'value'=>'rebootVal',
                'filter' => [0=>'Start / Do Nothing',1=>'Restart',2=>'Destroy'],
                'headerOptions' => ['style' => 'width:7em'],
                'contentOptions' => ['style' => 'white-space: nowrap;'],
            ],
            'team_allowed:boolean',
            [
                'attribute'=>'ts',
                'contentOptions' => ['style' => 'white-space: nowrap;'],
            ],
        ]
    ]); ?>


</div>
