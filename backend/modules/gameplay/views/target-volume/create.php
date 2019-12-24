<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\TargetVolume */

$this->title = 'Create Target Volume';
$this->params['breadcrumbs'][] = ['label' => 'Target Volumes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="target-volume-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
