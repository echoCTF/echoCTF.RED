<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\modules\frontend\models\PlayerToken $model */

$this->title = $model->player_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Player Tokens'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="player-token-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'player_id' => $model->player_id, 'type' => $model->type], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'player_id' => $model->player_id, 'type' => $model->type], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'player_id',
            'type',
            'token',
            'expires_at',
            'created_at',
        ],
    ]) ?>

</div>
