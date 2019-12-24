<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Arpdat */

$this->title = 'Update Arpdat: ' . $model->ip;
$this->params['breadcrumbs'][] = ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][] = ['label' => 'Arpdats', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ip, 'url' => ['view', 'ip' => $model->ip, 'mac' => $model->mac]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="arpdat-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
