<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\activity\models\PlayerDisconnectQueueHistory $model */

$this->title = Yii::t('app', 'Update Player Disconnect Queue History: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Disconnect Queue Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="player-disconnect-queue-history-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
