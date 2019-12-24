<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\Crl */

$this->title = Yii::t('app', 'Create Crl');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Crls'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crl-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
