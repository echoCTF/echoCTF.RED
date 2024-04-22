<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\NetworkTarget */

$this->title=Yii::t('app', 'Update Network Target: {name}', [
    'name' => $model->network_id,
]);
$this->params['breadcrumbs'][]=['label' => Yii::t('app', 'Network Targets'), 'url' => ['index']];
$this->params['breadcrumbs'][]=['label' => $model->network_id, 'url' => ['view', 'network_id' => $model->network_id, 'target_id' => $model->target_id]];
$this->params['breadcrumbs'][]=Yii::t('app', 'Update');
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/index.md'), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="network-target-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
