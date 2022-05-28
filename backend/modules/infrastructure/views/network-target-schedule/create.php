<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\NetworkTargetSchedule */

$this->title = Yii::t('app', 'Create Network Target Schedule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Network Target Schedules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="network-target-schedule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
