<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\TargetPlayerStateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Target Player States');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="target-player-state-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Target Player State'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute'=>'hostname',
                'contentOptions'=>['class'=>'text-nowrap'],
                'value'=>'target.name',
            ],
            [
                'attribute'=>'username',
                'contentOptions'=>['class'=>'text-nowrap'],
                'value'=>'player.username',
            ],
            'player_id',
            'player_treasures',
            'player_findings',
            'player_points',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
