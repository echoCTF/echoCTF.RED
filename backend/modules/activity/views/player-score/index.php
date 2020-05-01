<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\PlayerScoreSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title='Player Scores';
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="player-score-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Player Score', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'player_id',
            [
              'attribute' => 'player',
              'label'=>'Player',
              'value'=> function($model) {return sprintf("id:%d %s", $model->player_id, $model->player->username);},
            ],
            'points',
            'ts',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>


</div>
