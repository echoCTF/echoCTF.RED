<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\Headshot */

$this->title=Yii::t('app', 'Update Headshot: {name}', [
    'name' => $model->player_id,
]);
$this->params['breadcrumbs'][]=['label' => Yii::t('app', 'Headshots'), 'url' => ['index']];
$this->params['breadcrumbs'][]=['label' => $model->player_id, 'url' => ['view', 'player_id' => $model->player_id, 'target_id' => $model->target_id]];
$this->params['breadcrumbs'][]=Yii::t('app', 'Update');
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/create.md'), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="headshot-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
