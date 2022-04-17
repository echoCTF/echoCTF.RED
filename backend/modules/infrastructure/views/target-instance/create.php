<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\TargetInstance */

$this->title = Yii::t('app', 'Create Target Instance');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Target Instances'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="target-instance-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
