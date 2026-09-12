<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\activity\models\WsTokenHistory $model */

$this->title = Yii::t('app', 'Create Ws Token History');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ws Token Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ws-token-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
