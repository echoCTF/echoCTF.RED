<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\sales\models\ProductNetwork */

$this->title = Yii::t('app', 'Update Product Network: {name}', [
    'name' => $model->product_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['/sales/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Networks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->product_id, 'url' => ['view', 'product_id' => $model->product_id, 'network_id' => $model->network_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="product-network-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
