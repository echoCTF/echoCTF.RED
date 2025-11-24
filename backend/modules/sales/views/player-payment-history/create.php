<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\sales\models\PlayerPaymentHistory $model */

$this->title = Yii::t('app', 'Create Player Payment History');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Payment Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-payment-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
