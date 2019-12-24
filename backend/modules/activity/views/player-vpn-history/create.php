<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerVpnHistory */

$this->title = Yii::t('app', 'Create Player Vpn History');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Vpn Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-vpn-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
