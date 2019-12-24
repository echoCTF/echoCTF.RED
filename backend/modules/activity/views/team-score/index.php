<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\TeamScoreSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Team Scores';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-score-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Team Score', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'team_id',
            [
              'attribute' => 'team',
              'label'=>'Team',
              'value'=> function($model) {return sprintf("id:%d %s",$model->team_id,$model->team->name);},
            ],
            'points',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
