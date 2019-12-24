<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Hint */

$this->title = 'Create Hint';
$this->params['breadcrumbs'][] = ['label' => 'Hints', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hint-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
