<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Writeup */

$this->title = 'Update Writeup: ' . $model->player_id;
$this->params['breadcrumbs'][] = ['label' => 'Writeups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->player_id, 'url' => ['view', 'player_id' => $model->player_id, 'target_id' => $model->target_id]];
$this->params['breadcrumbs'][] = 'Update';
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap\Modal::end();
?>
<div class="writeup-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
