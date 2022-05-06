<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerScoreMonthly */

$this->title = Yii::t('app', 'Create Player Score Monthly');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Score Monthlies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-score-monthly-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
