<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\TargetVolume */

$this->title=$model->target_id;
$this->params['breadcrumbs'][]=ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][]=['label' => 'Target Volumes', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="target-volume-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'target_id' => $model->target_id, 'volume' => $model->volume], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'target_id' => $model->target_id, 'volume' => $model->volume], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
              'attribute' => 'target',
              'label'=>'Target',
              'value'=> function($model) {return sprintf("(id:%d) %s/%s", $model->target_id, $model->target->name, $model->target->ipoctet);},
            ],
            'volume',
            'bind',
            'ts',

        ],
    ]) ?>

</div>
