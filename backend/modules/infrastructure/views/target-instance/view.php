<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\TargetInstance */

$this->title = "Instance for player: ".$model->player_id;
$this->params['breadcrumbs'][]=ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Target Instances'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="target-instance-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->player_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Logs', ['logs', 'id' => $model->player_id], ['class' => 'btn btn-info']) ?>
        <?= Html::a('Exec', ['exec', 'id' => $model->player_id], ['class' => 'btn btn-danger','style'=>'background: black; color: white']) ?>
        <?= Html::a('Restart', ['restart', 'id' => $model->player_id], [
            'class' => 'btn btn-warning',
            'data' => [
                'confirm' => 'Are you sure you want to restart the host?',
                'method' => 'post',
            ],
        ]) ?>
                <?= Html::a('Delete', ['delete', 'id' => $model->player_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Destroy', ['destroy', 'id' => $model->player_id], [
            'class' => 'btn btn-info',
            'data' => [
                'confirm' => 'Are you sure you want to destroy the container for this item?',
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
            'team_allowed:boolean',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
