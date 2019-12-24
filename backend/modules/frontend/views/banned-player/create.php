<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\BannedPlayer */

$this->title = Yii::t('app', 'Create Banned Player');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Banned Players'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="banned-player-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
