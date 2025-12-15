<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\activity\models\WsToken $model */

$this->title = Yii::t('app', 'Create Ws Token');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ws Tokens'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ws-token-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
