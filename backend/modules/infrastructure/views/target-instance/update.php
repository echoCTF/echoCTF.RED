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
?>
<div class="target-instance-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
