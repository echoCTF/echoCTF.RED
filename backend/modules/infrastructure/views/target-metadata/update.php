<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\TargetMetadata */

$this->title = 'Update Target Metadata: ' . $model->target_id;
$this->params['breadcrumbs'][] = ['label' => 'Target Metadatas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->target_id, 'url' => ['view', 'id' => $model->target_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="target-metadata-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
