<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerLast */

$this->title=Yii::t('app', 'Update Player Last: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][]=['label' => Yii::t('app', 'Player Lasts'), 'url' => ['index']];
$this->params['breadcrumbs'][]=['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][]=Yii::t('app', 'Update');
?>
<div class="player-last-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
