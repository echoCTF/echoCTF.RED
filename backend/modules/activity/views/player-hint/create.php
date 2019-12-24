<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerHint */

$this->title = 'Create Player Hint';
$this->params['breadcrumbs'][] = ['label' => 'Player Hints', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-hint-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
