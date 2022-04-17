<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\TargetInstance */

$this->title = "Instance for player: ".$model->player_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Target Instances'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="target-instance-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->player_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->player_id], [
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
            [
              'label'=>'player',
              'value'=>$model->player_id.': '.$model->player->username
            ],
            [
              'label'=>'target',
              'value'=>$model->target_id.': '.$model->target->name
            ],
            [
              'label'=>'server',
              'value'=>$model->server_id.': '.$model->server->name
            ],
            'ipoctet',
            'reboot',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
