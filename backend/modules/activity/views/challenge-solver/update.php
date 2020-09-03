<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\ChallengeSolver */

$this->title = 'Update Challenge Solver: ' . $model->challenge_id;
$this->params['breadcrumbs'][] = ['label' => 'Challenge Solvers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->challenge_id, 'url' => ['view', 'challenge_id' => $model->challenge_id, 'player_id' => $model->player_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="challenge-solver-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
