<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\infrastructure\models\PrivateNetwork $model */

$this->title = Yii::t('app', 'Create Private Network');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Private Networks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="private-network-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
