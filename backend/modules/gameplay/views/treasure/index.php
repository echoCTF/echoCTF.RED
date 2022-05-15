<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\gameplay\models\TreasureSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][]=$this->title;
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.$this->title.' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap\Modal::end();
?>
<div class="treasure-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Treasure', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'target_id',
            [
                'attribute' => 'ipoctet',
                'label'=>'Target',
                'format'=>'raw',
                'value'=> function($model) {return sprintf("<small>%s/%s</small>", $model->target->name, $model->target->ipoctet);},
            ],
            'name',
            'pubname',
            [
              'attribute'=>'category',
              'filter'=>['other'=>'other', 'env'=>'env', 'root'=>'root', 'system'=>'system', 'app'=>'app'],
            ],
//            'description:ntext',
//            'pubdescription:ntext',
            'points',
            [
              'attribute'=>'player_type',
              'filter'=>['offense'=>'Offense', 'defense'=>'Defense', 'both'=>'Both'],
            ],
//            'csum',
            'appears',
//            [
//              'attribute'=>'effects',
//              'filter'=>['player'=>'Player', 'team'=>'Team','total'=>'Total'],
//            ],

            [
              'attribute'=>'code',
              'format'=>'raw',
              'value'=>function($model) {return '<abbr title="'.Html::encode($model->code).'">'.substr($model->code, 0, 15).'</abbr>';},
            ],

            [
              'attribute'=>'discovered',
              'value'=>function($model) {return count($model->playerTreasures);},
              'filter'=>[0=>'No', 1=>'Yes'],
            ],
            'weight',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>


</div>
