<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\TargetInstance */

$this->title = Yii::t('app', 'Update Target Instance: {target} for {username}', [
  'username' => $model->player->username,
  'target' => $model->target->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Target Instances'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->player_id, 'url' => ['view', 'id' => $model->player_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
yii\bootstrap\Modal::begin([
  'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.$this->title.' Help</h2>',
  'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/index.md'), 'gfm');
yii\bootstrap\Modal::end();

?>
<div class="target-instance-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
