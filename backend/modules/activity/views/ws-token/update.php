<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\activity\models\WsToken $model */

$this->title = Yii::t('app', 'Update Ws Token: {name}', [
    'name' => $model->token,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ws Tokens'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->token, 'url' => ['view', 'token' => $model->token]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="ws-token-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
