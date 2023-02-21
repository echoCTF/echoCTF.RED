<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerScore */

$this->title='Update Player Score: '.$model->player_id;
$this->params['breadcrumbs'][]=['label' => 'Player Scores', 'url' => ['index']];
$this->params['breadcrumbs'][]=['label' => $model->player_id, 'url' => ['view', 'id' => $model->player_id]];
$this->params['breadcrumbs'][]='Update';
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="player-score-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
