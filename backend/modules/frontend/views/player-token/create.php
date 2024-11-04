<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\frontend\models\PlayerToken $model */

$this->title = Yii::t('app', 'Create Player Token');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Tokens'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-token-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
