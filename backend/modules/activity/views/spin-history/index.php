<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\SpinHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=Yii::t('app', 'Spin Histories');
$this->params['breadcrumbs'][]=$this->title;
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap\Modal::end();
?>
<div class="spin-history-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Truncate'), ['truncate'], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to truncate this table?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'target_id',
            [
              'attribute' => 'target',
              'label'=>'Target',
              'value'=> function($model) {return sprintf("id:%d %s", $model->target_id, $model->target->name);},
            ],
            'player_id',
            [
              'attribute' => 'player',
              'label'=>'Player',
              'value'=> function($model) {return sprintf("id:%d %s", $model->player_id, $model->player->username);},
            ],
            'created_at:dateTime',
            'updated_at:dateTime',

        ],
    ]);?>


</div>
