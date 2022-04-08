<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\infrastructure\models\TargetInstanceAuditSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Target Instance Audits');
$this->params['breadcrumbs'][] = $this->title;
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.$this->title.' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap\Modal::end();
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
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'op',
            'player_id',
            'target_id',
            'server_id',
            'ip',
            'reboot',
            'ts',
        ]
    ]); ?>


</div>
