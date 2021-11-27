<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\TargetVariable */

$this->title='Create Target Variable';
$this->params['breadcrumbs'][]=['label' => 'Target Variables', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="target-variable-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
