<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\SpinQueue */

$this->title=Yii::t('app', 'Create Spin Queue');
$this->params['breadcrumbs'][]=['label' => Yii::t('app', 'Spin Queues'), 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="spin-queue-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
