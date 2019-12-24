<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\smartcity\models\Infrastructure */

$this->title = 'Update Infrastructure: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Infrastructures', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="infrastructure-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
