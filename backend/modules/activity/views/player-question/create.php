<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerQuestion */

$this->title='Create Player Question';
$this->params['breadcrumbs'][]=['label' => 'Player Questions', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="player-question-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
