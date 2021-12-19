<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerCounterNf */

$this->title = Yii::t('app', 'Create Player Counter Nf');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Counter Nfs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-counter-nf-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
