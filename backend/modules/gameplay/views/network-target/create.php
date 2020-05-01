<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\NetworkTarget */

$this->title=Yii::t('app', 'Create Network Target');
$this->params['breadcrumbs'][]=['label' => Yii::t('app', 'Network Targets'), 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="network-target-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
