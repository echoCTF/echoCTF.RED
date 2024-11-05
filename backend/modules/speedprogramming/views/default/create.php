<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SpeedSolution */

$this->title = 'Create Speed Solution';
$this->params['breadcrumbs'][] = ['label' => 'Speed Solutions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="speed-solution-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
