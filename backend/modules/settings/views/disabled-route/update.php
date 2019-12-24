<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\DisabledRoute */

$this->title = 'Update Disabled Route: ' . $model->route;
$this->params['breadcrumbs'][] = ['label' => 'Disabled Routes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->route, 'url' => ['view', 'id' => $model->route]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="disabled-route-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
