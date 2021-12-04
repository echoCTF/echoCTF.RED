<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Target */

$this->title='Create Target';
$this->params['breadcrumbs'][]=['label' => 'Targets', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.$this->title.' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo $this->render('help/'.$this->context->action->id);
yii\bootstrap\Modal::end();
?>
<div class="target-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>Note: You will have to manualy add the three needed images (<code>name.png, _name.png, _name-thumbnail.png</code>) under <code>frontend/web/images/targets</code></p>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
