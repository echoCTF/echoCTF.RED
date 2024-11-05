<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SpeedSolution */

$this->title = 'Update Speed Solution: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Speed Solutions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="speed-solution-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
