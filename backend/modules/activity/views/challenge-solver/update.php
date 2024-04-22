<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\ChallengeSolver */

$this->title = 'Update Challenge Solver: ' . $model->challenge_id;
$this->params['breadcrumbs'][] = ['label' => 'Challenge Solvers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->challenge_id, 'url' => ['view', 'challenge_id' => $model->challenge_id, 'player_id' => $model->player_id]];
$this->params['breadcrumbs'][] = 'Update';
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/index.md'), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="challenge-solver-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
