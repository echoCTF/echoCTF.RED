<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\ChallengeSolver */

$this->title = $model->challenge_id;
$this->params['breadcrumbs'][] = ['label' => 'Challenge Solvers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="challenge-solver-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'challenge_id' => $model->challenge_id, 'player_id' => $model->player_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'challenge_id' => $model->challenge_id, 'player_id' => $model->player_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'challenge_id',
            'player_id',
            'timer:datetime',
            'rating',
            'first',
            'created_at',
        ],
    ]) ?>

</div>
