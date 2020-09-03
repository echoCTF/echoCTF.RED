<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\ChallengeSolver */

$this->title = 'Create Challenge Solver';
$this->params['breadcrumbs'][] = ['label' => 'Challenge Solvers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="challenge-solver-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
