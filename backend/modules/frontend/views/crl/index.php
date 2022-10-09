<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\frontend\models\CrlSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=Yii::t('app', 'Crls');
$this->params['breadcrumbs'][]=$this->title;
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.$this->title.' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap\Modal::end();
?>
<div class="crl-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Crl'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Delete All'), ['truncate'], [
            'class' => 'btn btn-warning',
            'data' => [
                'confirm' => 'Are you sure you want to truncate the CRL?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'columns' => [

            'id',
            'player_id',
            [
              'attribute'=>'subject',
              'value'=>function($model) { return wordwrap($model->subject, 80, "\n", true);}
            ],
            //'csr:ntext',
            //'crt:ntext',
            //'privkey:ntext',
            'ts',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>


</div>
