<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Arpdat */

$this->title = 'Create Arpdat';
$this->params['breadcrumbs'][] = ['label' => 'Arpdats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="arpdat-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
