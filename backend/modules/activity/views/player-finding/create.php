<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerFinding */

$this->title = 'Create Player Finding';
$this->params['breadcrumbs'][] = ['label' => 'Player Findings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-finding-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
