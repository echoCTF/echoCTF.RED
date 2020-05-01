<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerTreasure */

$this->title='Create Player Treasure';
$this->params['breadcrumbs'][]=['label' => 'Player Treasures', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="player-treasure-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
