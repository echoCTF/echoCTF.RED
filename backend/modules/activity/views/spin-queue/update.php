<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\SpinQueue */

$this->title = Yii::t('app', 'Update Spin Queue: {name}', [
    'name' => $model->target_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Spin Queues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->target_id, 'url' => ['view', 'id' => $model->target_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="spin-queue-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
