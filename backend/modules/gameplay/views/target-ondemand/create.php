<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\TargetOndemand */

$this->title = Yii::t('app', 'Create Target Ondemand');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Target Ondemands'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="target-ondemand-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
