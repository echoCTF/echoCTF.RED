<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Writeup */

$this->title = 'Create Writeup';
$this->params['breadcrumbs'][] = ['label' => 'Writeups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="writeup-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
