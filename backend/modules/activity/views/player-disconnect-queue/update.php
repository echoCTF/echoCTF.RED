<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\activity\models\PlayerDisconnectQueue $model */

$this->title = Yii::t('app', 'Update Player Disconnect Queue: {name}', [
    'name' => $model->player_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Disconnect Queues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->player_id, 'url' => ['view', 'player_id' => $model->player_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="player-disconnect-queue-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
