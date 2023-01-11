<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Target */

$this->title="View logs for ".$model->name."/".$model->ipoctet." running on ".($model->server ? $model->server->name: "localhost");
$this->params['breadcrumbs'][]=ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Target Instances'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="target-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('View', ['view', 'id' => $model->player_id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Update', ['update', 'id' => $model->player_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Exec', ['exec', 'id' => $model->player_id], ['class' => 'btn btn-info']) ?>
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
        <?= Html::a('Generate', ['generate', 'id' => $model->player_id], ['class' => 'btn btn-info', 'style'=>'background-color: gray']) ?>
        <?= Html::a('Spin', ['spin', 'id' => $model->player_id], [
            'class' => 'btn btn-warning',
            'data' => [
                'confirm' => 'Are you sure you want to restart the host?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Pull', ['pull', 'id' => $model->player_id], [
            'class' => 'btn btn-success',
            'data' => [
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

<pre>
<?=Html::encode($logs)?>
</pre>
</div>
