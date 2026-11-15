<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\modules\frontend\models\PlayerAudit $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Players Audit'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="player-audit-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
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
            'id',
            'player_id',
            [
                'label' => Yii::t('app', 'Current Username'),
                'format' => 'raw',
                'value' => $model->player && $model->player->profile ? Html::a(Html::encode($model->player->username), ['/frontend/profile/view-full', 'id' => $model->player->profile->id]) : Yii::t('app', 'player deleted'),
            ],
            'field',
            'old_value',
            'new_value',
            'ts',
        ],
    ]) ?>

</div>
