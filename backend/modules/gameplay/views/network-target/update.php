<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\NetworkTarget */

$this->title = Yii::t('app', 'Update Network Target: {name}', [
    'name' => $model->network_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Network Targets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->network_id, 'url' => ['view', 'network_id' => $model->network_id, 'target_id' => $model->target_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="network-target-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
