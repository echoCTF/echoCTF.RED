<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\sales\models\StripeWebhook */

$this->title = Yii::t('app', 'Create Stripe Webhook');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['/sales/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stripe Webhooks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stripe-webhook-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
