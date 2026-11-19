<?php

use app\modules\moderation\models\SharedNetwork;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\moderation\models\SharedNetwork $model */

$this->title = Yii::t('app', 'Update Shared Network: {cidr}', ['cidr' => $model->cidr]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Shared Networks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cidr, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="shared-network-update">

  <h1><?= Html::encode($this->title) ?></h1>

  <?= $this->render('_form', [
    'model' => $model,
  ]) ?>

</div>
