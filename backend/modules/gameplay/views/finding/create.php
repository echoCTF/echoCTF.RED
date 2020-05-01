<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Finding */

$this->title='Create Finding';
$this->params['breadcrumbs'][]=['label' => 'Findings', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="finding-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
