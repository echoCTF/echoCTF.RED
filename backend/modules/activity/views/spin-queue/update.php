<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\SpinQueue */

$this->title=Yii::t('app', 'Update Spin Queue: {name}', [
    'name' => $model->target_id,
]);
$this->params['breadcrumbs'][]=['label' => Yii::t('app', 'Spin Queues'), 'url' => ['index']];
$this->params['breadcrumbs'][]=['label' => $model->target_id, 'url' => ['view', 'id' => $model->target_id]];
$this->params['breadcrumbs'][]=Yii::t('app', 'Update');
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo $this->render('help/'.$this->context->action->id);
yii\bootstrap5\Modal::end();
?>
<div class="spin-queue-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
