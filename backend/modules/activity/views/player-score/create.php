<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerScore */

$this->title='Create Player Score';
$this->params['breadcrumbs'][]=['label' => 'Player Scores', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="player-score-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
