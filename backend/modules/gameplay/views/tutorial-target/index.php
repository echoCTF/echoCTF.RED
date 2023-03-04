<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\gameplay\models\TutorialTargetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Tutorial Targets');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="tutorial-target-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Tutorial Target'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'tutorial_id',
            'tutorial.title',
            'target_id',
            'target.name',
            'target.ipoctet',
            'weight',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
