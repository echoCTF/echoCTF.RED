<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\NetworkPlayer */

$this->title=Yii::t('app', 'Create Network Player');
$this->params['breadcrumbs'][]=['label' => Yii::t('app', 'Network Players'), 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="network-player-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
