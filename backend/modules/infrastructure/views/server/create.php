<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\Server */

$this->title = Yii::t('app', 'Create Server');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Servers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="server-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
