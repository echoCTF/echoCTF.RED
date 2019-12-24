<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\TeamScore */

$this->title = 'Update Team Score: ' . $model->team_id;
$this->params['breadcrumbs'][] = ['label' => 'Team Scores', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->team_id, 'url' => ['view', 'id' => $model->team_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="team-score-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
