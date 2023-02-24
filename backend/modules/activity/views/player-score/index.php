<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\PlayerScoreSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Player Scores';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> ' . Html::encode($this->title) . ' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/' . $this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="player-score-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Player Score', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'player_id',
            ['class' => 'app\components\columns\ProfileColumn', 'attribute' => 'player'],
            'points',
            'ts',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>