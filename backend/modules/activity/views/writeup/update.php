<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Writeup */

$this->title = 'Update Writeup: ' . $model->player_id;
$this->params['breadcrumbs'][] = ['label' => 'Writeups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->player_id, 'url' => ['view', 'player_id' => $model->player_id, 'target_id' => $model->target_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="writeup-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
