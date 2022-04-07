<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\TargetInstanceAudit */

$this->title = Yii::t('app', 'Create Target Instance Audit');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Target Instance Audits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="target-instance-audit-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
