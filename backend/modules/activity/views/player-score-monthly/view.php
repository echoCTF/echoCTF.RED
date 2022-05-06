<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerScoreMonthly */

$this->title = $model->player_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Score Monthlies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="player-score-monthly-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'player_id' => $model->player_id, 'dated_at' => $model->dated_at], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'player_id' => $model->player_id, 'dated_at' => $model->dated_at], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'player_id',
            'player.username',
            'points',
            'dated_at',
            'ts',
        ],
    ]) ?>

</div>
