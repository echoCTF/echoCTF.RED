<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\TargetOndemand */

$this->title = Yii::t('app', 'Update Target Ondemand: {name}', [
    'name' => $model->target_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Target Ondemands'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->target_id, 'url' => ['view', 'id' => $model->target_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="target-ondemand-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
