<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerScore */

$this->title = 'Update Player Score: ' . $model->player_id;
$this->params['breadcrumbs'][] = ['label' => 'Player Scores', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->player_id, 'url' => ['view', 'id' => $model->player_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="player-score-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
