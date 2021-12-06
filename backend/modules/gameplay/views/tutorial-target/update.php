<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\TutorialTarget */

$this->title = Yii::t('app', 'Update Tutorial Target: {name}', [
    'name' => $model->tutorial_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tutorial Targets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->tutorial_id, 'url' => ['view', 'tutorial_id' => $model->tutorial_id, 'target_id' => $model->target_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.$this->title.' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap\Modal::end();
?>
<div class="tutorial-target-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
