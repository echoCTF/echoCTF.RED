<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Headshot */

$this->title=Yii::t('app', 'Create Headshot');
$this->params['breadcrumbs'][]=['label' => Yii::t('app', 'Headshots'), 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/create.md'), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="headshot-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
