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
        <?= Html::a('Logs', ['logs', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
        <?= Html::a('Exec', ['exec', 'id' => $model->id], ['class' => 'btn btn-danger','style'=>'background: black; color: white']) ?>
        <?= Html::a('Generate', ['generate', 'id' => $model->id], ['class' => 'btn btn-info', 'style'=>'background-color: gray']) ?>
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
            'ipoctet',
            [
              'label'=>'Network',
              'attribute'=>'network.name'
            ],
            'difficulty',
            'category',
            'required_xp',
            'suggested_xp',
            'timer:boolean',
            'rootable:boolean',
            'active:boolean',
            'healthcheck:boolean',
            'writeup_allowed:boolean',
            'headshot_spin:boolean',
            'player_spin:boolean',
            'instance_allowed:boolean',
            'require_findings:boolean',
            'status',
            'scheduled_at',
            'purpose',
            'description:html',
            'mac',
            'net',
            'server',
            'image',
            'dns',
            'parameters',
            'weight',
            'created_at',
            'ts',
            [
              'label'=>'Examples',
              'format'=>'raw',
              'value'=>function($model){ return '<pre>'.sprintf("docker run -itd \\\n--name %s \\\n--dns %s \\\n--hostname %s \\\n--ip %s \\\n--mac-address %s \\\n--network %s \\\n%s", $model->name,$model->dns,$model->fqdn,$model->ipoctet,$model->mac,$model->net,$model->image).'</pre>'; }
            ],

        ],
    ]) ?>

</div>
