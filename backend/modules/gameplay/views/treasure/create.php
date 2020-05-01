<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Treasure */

$this->title='Create Treasure';
$this->params['breadcrumbs'][]=['label' => 'Treasures', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="treasure-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
