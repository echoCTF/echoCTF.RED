<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\speedprogramming\models\SpeedProblem $model */

$this->title = Yii::t('app', 'Create Speed Problem');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Speed Problems'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="speed-problem-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
