<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerTargetHelp */

$this->title = Yii::t('app', 'Create Player Target Help');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Target Helps'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-target-help-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
