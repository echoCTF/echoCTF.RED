<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\TeamScore */

$this->title = 'Create Team Score';
$this->params['breadcrumbs'][] = ['label' => 'Team Scores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-score-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
