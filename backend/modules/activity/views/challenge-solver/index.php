<?php

use yii\helpers\Html;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\ChallengeSolverSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Challenge Solvers';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];

yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/index.md'), 'gfm');
yii\bootstrap5\Modal::end();
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
            ['class' => 'app\components\columns\ProfileColumn'],
            'challenge_id',
            [
              'attribute'=>'challenge_name',
              'value'=>'challenge.name',
              'headerOptions' => ['style' => 'width:20vw'],
            ],
            'timer',
            'rating',
            'first',
            'created_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
