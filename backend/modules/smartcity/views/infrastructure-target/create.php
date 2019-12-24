<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\InfrastructureTarget */

$this->title = 'Create Infrastructure Target';
$this->params['breadcrumbs'][] = ['label' => 'Infrastructure Targets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="infrastructure-target-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
