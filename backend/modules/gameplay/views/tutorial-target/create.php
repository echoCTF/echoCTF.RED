<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\TutorialTarget */

$this->title = Yii::t('app', 'Create Tutorial Target');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tutorial Targets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tutorial-target-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
