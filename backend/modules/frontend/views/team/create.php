<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\Team */

$this->title='Create Team';
$this->params['breadcrumbs'][]=['label' => 'Teams', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="team-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
