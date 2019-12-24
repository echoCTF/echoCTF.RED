<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerBadge */

$this->title = 'Create Player Badge';
$this->params['breadcrumbs'][] = ['label' => 'Player Badges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-badge-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
