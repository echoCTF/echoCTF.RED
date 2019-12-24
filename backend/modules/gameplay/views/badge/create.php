<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Badge */

$this->title = 'Create Badge';
$this->params['breadcrumbs'][] = ['label' => 'Badges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="badge-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
