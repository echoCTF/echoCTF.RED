<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\PlayerSsl */

$this->title = 'Update Player Ssl: ' . $model->player_id;
$this->params['breadcrumbs'][] = ['label' => 'Player Ssls', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->player_id, 'url' => ['view', 'id' => $model->player_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="player-ssl-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
