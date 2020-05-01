<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\SpinHistory */

$this->title=Yii::t('app', 'Create Spin History');
$this->params['breadcrumbs'][]=['label' => Yii::t('app', 'Spin Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="spin-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
