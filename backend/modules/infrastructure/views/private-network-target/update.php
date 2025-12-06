<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\infrastructure\models\PrivateNetworkTarget $model */

$this->title = Yii::t('app', 'Update Private Network Target: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Private Network Targets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="private-network-target-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
