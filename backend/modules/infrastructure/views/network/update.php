<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Network */

$this->title=Yii::t('app', 'Update Network: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][]=['label' => Yii::t('app', 'Networks'), 'url' => ['index']];
$this->params['breadcrumbs'][]=['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][]=Yii::t('app', 'Update');
?>
<div class="network-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
