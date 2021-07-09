<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\TargetMetadata */

$this->title = 'Create Target Metadata';
$this->params['breadcrumbs'][] = ['label' => 'Infrastructure', 'url' => ['/infrastructure/default/index']];
$this->params['breadcrumbs'][] = ['label' => 'Target metadata', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="target-metadata-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
