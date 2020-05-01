<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Treasure */

$this->title='Update Treasure: '.$model->name;
$this->params['breadcrumbs'][]=ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][]=['label' => 'Treasures', 'url' => ['index']];
$this->params['breadcrumbs'][]=['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][]='Update';
?>
<div class="treasure-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
