<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\infrastructure\models\PrivateNetworkTarget $model */

$this->title = Yii::t('app', 'Create Private Network Target');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Private Network Targets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="private-network-target-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
