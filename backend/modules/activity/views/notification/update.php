<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Notification */

$this->title='Update Notification: '.$model->title;
$this->params['breadcrumbs'][]=['label' => 'Notifications', 'url' => ['index']];
$this->params['breadcrumbs'][]=['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][]='Update';
?>
<div class="notification-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
