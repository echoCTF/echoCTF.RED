<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Stream */

$this->title='Update Stream: '.$model->title;
$this->params['breadcrumbs'][]=ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][]=['label' => 'Streams', 'url' => ['index']];
$this->params['breadcrumbs'][]=['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][]='Update';
?>
<div class="stream-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
