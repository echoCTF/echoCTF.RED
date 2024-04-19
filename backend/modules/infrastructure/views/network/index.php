<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\gameplay\models\NetworkSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=Yii::t('app', 'Networks');
$this->params['breadcrumbs'][]=['label' => 'Networks', 'url' => ['index']];
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/index.md'), 'gfm');
yii\bootstrap5\Modal::end();

?>
<div class="network-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Network'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute'=>'icon',
                'format'=>'html',
                'contentOptions'=>['class'=>'text-center'],
                'value'=>function($model){
                    if(substr($model->icon,0,1)!=='/') {
                        return $model->icon;
                    }
                    return Html::img('//'.Yii::$app->sys->offense_domain.$model->icon,['style'=>'max-width: 50px;','class'=>'img-thumbnail']);
                }
            ],
            'codename:text',
            [
                'attribute'=>'name',
                'contentOptions'=>['style'=>'white-space: nowrap;']

            ],
            [
                'attribute'=>'description',
                'contentOptions'=>['class'=>'font-weight-light small text-monospace'],
                'format'=>'html',
            ],
            [
                'attribute'=>'public',
                'format'=>'boolean',
                'contentOptions'=>['class'=>'text-center'],
            ],
            [
                'attribute'=>'guest',
                'format'=>'boolean',
                'contentOptions'=>['class'=>'text-center'],
            ],
            [
                'attribute'=>'active',
                'format'=>'boolean',
                'contentOptions'=>['class'=>'text-center'],
            ],
            [
                'attribute'=>'announce',
                'format'=>'boolean',
                'contentOptions'=>['class'=>'text-center'],
            ],
            [
                'label'=>'Targets',
                'format'=>'integer',
                'attribute'=>'network_targets',
                'value'=>function($model){ return count($model->networkTargets); },
                'contentOptions'=>['class'=>'text-center'],
            ],
            [
                'label'=>'Players',
                'attribute'=>'network_players',
                'format'=>'integer',
                'value'=>function($model){ return count($model->networkPlayers); },
                'contentOptions'=>['class'=>'text-center'],
            ],
            'weight:integer',
//            'ts',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>


</div>
