<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\TargetPlayerState */

$this->title = Yii::t('app', 'Create Target Player State');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Target Player States'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="target-player-state-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
