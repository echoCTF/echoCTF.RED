<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\PlayerDisabledroute */

$this->title = Yii::t('app', 'Create Player Disabledroute');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Disabledroutes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-disabledroute-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
