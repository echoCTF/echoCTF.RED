<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\TargetState */

$this->title = Yii::t('app', 'Create Target State');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Target States'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="target-state-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
