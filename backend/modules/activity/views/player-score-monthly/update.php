<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerScoreMonthly */

$this->title = Yii::t('app', 'Update Player Score Monthly: {name}', [
    'name' => $model->player_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Score Monthlies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->player_id, 'url' => ['view', 'player_id' => $model->player_id, 'dated_at' => $model->dated_at]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="player-score-monthly-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
