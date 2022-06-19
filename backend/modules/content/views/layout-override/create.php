<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\content\models\LayoutOverride */

$this->title = Yii::t('app', 'Create Layout Override');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Layout Overrides'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="layout-override-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
