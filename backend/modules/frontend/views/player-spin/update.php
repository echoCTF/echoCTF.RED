<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\PlayerSpin */

$this->title=Yii::t('app', 'Update Player Spin: {name}', [
    'name' => $model->player_id,
]);
$this->params['breadcrumbs'][]=['label' => Yii::t('app', 'Player Spins'), 'url' => ['index']];
$this->params['breadcrumbs'][]=['label' => $model->player_id, 'url' => ['view', 'id' => $model->player_id]];
$this->params['breadcrumbs'][]=Yii::t('app', 'Update');
?>
<div class="player-spin-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
