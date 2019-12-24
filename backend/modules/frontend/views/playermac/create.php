<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\PlayerMac */

$this->title = 'Create Player Mac';
$this->params['breadcrumbs'][] = ['label' => 'Player Macs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-mac-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
