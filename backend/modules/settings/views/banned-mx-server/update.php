<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\BannedMxServer */

$this->title = Yii::t('app', 'Update Banned Mx Server: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Banned Mx Servers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.$this->title.' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap\Modal::end();
?>
<div class="banned-mx-server-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
