<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerTargetHelp */

$this->title = Yii::t('app', 'Update Player Target Help: {name}', [
    'name' => $model->player_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Target Helps'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->player_id, 'url' => ['view', 'player_id' => $model->player_id, 'target_id' => $model->target_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="player-target-help-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
