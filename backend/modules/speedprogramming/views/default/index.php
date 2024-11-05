<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\speedprogramming\models\SpeedSolution;
/* @var $this yii\web\View */
/* @var $searchModel app\models\SpeedSolutionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Speed Solutions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="speed-solution-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Speed Solution', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            ['class' => 'app\components\columns\ProfileColumn','attribute'=>'username','idkey'=>'profile.id','field'=>'player.username'],
            [
              'attribute'=>'problem',
              'value'=>'problem.name',
            ],

            [
               'attribute' => 'language',
               'filter'    => SpeedSolution::getLanguages(),
            ],
            [
               'attribute' => 'status',
               'filter'    => SpeedSolution::getStatuses(),
            ],
            //'sourcecode',
            'points',
            //'modcomments:ntext',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn','template' => '{view} {update}'],
        ],
    ]); ?>


</div>
