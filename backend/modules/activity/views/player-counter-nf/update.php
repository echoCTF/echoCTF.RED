<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerCounterNf */

$this->title = Yii::t('app', 'Update Player Counter Nf: {name}', [
    'name' => $model->player_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Counter Nfs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->player_id, 'url' => ['view', 'player_id' => $model->player_id,'metric'=>$model->metric]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="player-counter-nf-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
