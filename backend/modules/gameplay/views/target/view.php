<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Target */

$this->title=$model->name;
$this->params['breadcrumbs'][]=ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][]=['label' => 'Targets', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="target-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Spin', ['spin', 'id' => $model->id], [
            'class' => 'btn btn-warning',
            'data' => [
                'confirm' => 'Are you sure you want to restart the host?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Pull', ['pull', 'id' => $model->id], [
            'class' => 'btn btn-success',
            'data' => [
                'method' => 'post',
            ],
        ]) ?>

        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Destroy', ['destroy', 'id' => $model->id], [
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
            'id',
            'name',
            'fqdn',
            'difficulty',
            'required_xp',
            'suggested_xp',
            'rootable:boolean',
            'active:boolean',
            'status',
            'scheduled_at',
            'purpose',
            'description:ntext',
            'ipoctet',
            'mac',
            'net',
            'server',
            'image',
            'dns',
            'parameters',
            'ts',

        ],
    ]) ?>

</div>
