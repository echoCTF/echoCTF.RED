<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\activity\models\PlayerDisconnectQueueHistory $model */

$this->title = Yii::t('app', 'Create Player Disconnect Queue History');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Disconnect Queue Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-disconnect-queue-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
