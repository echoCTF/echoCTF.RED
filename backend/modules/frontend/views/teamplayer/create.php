<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\TeamPlayer */

$this->title='Create Team Player';
$this->params['breadcrumbs'][]=['label' => 'Team Players', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="team-player-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
