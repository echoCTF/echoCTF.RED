<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\Objective */

$this->title='Create Objective';
$this->params['breadcrumbs'][]=['label' => 'Objectives', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="objective-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
