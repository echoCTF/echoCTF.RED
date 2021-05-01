<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\Instruction */

$this->title='Create Instruction';
$this->params['breadcrumbs'][]=['label' => 'Instructions', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="instruction-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
