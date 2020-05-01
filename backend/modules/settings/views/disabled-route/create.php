<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\DisabledRoute */

$this->title='Create Disabled Route';
$this->params['breadcrumbs'][]=['label' => 'Disabled Routes', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="disabled-route-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
