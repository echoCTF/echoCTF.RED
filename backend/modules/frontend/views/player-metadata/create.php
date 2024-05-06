<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\frontend\models\PlayerMetadata $model */

$this->title = Yii::t('app', 'Create Player Metadata');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Metadatas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
yii\bootstrap5\Modal::begin([
  'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
  'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="player-metadata-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
