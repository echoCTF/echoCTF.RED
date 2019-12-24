<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\PlayerIp */

$this->title = 'Create Player Ip';
$this->params['breadcrumbs'][] = ['label' => 'Player Ips', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-ip-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
