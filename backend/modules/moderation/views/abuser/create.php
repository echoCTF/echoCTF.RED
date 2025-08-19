<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\frontend\models\Abuser $model */

$this->title = Yii::t('app', 'Create Abuser');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Abusers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="abuser-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
