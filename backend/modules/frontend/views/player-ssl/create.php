<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\PlayerSsl */

$this->title='Create Player Ssl';
$this->params['breadcrumbs'][]=['label' => 'Player Ssls', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="player-ssl-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
