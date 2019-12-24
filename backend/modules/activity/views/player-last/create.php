<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerLast */

$this->title = Yii::t('app', 'Create Player Last');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Lasts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-last-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
