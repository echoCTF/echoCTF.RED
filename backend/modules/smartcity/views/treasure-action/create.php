<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\TreasureAction */

$this->title = 'Create Treasure Action';
$this->params['breadcrumbs'][] = ['label' => 'Treasure Actions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="treasure-action-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
