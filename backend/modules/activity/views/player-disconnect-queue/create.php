<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\activity\models\PlayerDisconnectQueue $model */

$this->title = Yii::t('app', 'Create Player Disconnect Queue');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Disconnect Queues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-disconnect-queue-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
