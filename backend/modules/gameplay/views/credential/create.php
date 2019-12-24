<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Credential */

$this->title = 'Create Credential';
$this->params['breadcrumbs'][] = ['label' => 'Credentials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="credential-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
