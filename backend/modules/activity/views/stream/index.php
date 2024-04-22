<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\StreamSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="stream-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Stream', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php \yii\widgets\Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'columns' => [
            'id',
            [
              'attribute'=>'formatted',
              'format'=>'html',
              'headerOptions' => ['style' => 'width:50%'],
            ],
/*            [
                'attribute' => 'player',
                'label'=>'Player',
                'value'=> function($model) {return sprintf("id:%d %s",$model->player_id,$model->player->username);},
            ],*/
            'player_id',
            'model',
            'model_id',
            'points',
//            'ts',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>
    <?php \yii\widgets\Pjax::end(); ?>


</div>
