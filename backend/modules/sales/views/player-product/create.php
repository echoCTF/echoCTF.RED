<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\sales\models\PlayerProduct $model */

$this->title = Yii::t('app', 'Create Player Product');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-product-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
