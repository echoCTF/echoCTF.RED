<?php

use app\modules\moderation\models\SharedNetwork;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\moderation\models\SharedNetwork $model */

$this->title = Yii::t('app', 'Create Shared Network');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Shared Networks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shared-network-create">

  <h1><?= Html::encode($this->title) ?></h1>

  <?= $this->render('_form', [
    'model' => $model,
  ]) ?>

</div>
