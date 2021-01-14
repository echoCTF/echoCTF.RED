<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\ChallengeSolverSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Challenge Solvers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="challenge-solver-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Challenge Solver', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'player_id',
            [
              'attribute'=>'username',
              'value'=>'player.username'
            ],
            'challenge_id',
            [
              'attribute'=>'challenge_name',
              'value'=>'challenge.name',
              'headerOptions' => ['style' => 'width:20vw'],
            ],
            'timer',
            'rating',
            'first',
            'created_at:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
