<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\activity\models\PlayerBandwidth $model */

$this->title = Yii::t('app', 'Create Player Bandwidth');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Bandwidths'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-bandwidth-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
