<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\VpnTemplate */

$this->title = Yii::t('app', 'Create Vpn Template');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Vpn Templates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="vpn-template-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
