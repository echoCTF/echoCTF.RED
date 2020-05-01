<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\InfrastructureTarget */

$this->title=$model->infrastructure_id;
$this->params['breadcrumbs'][]=['label' => 'Infrastructure Targets', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="infrastructure-target-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'infrastructure_id' => $model->infrastructure_id, 'target_id' => $model->target_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'infrastructure_id' => $model->infrastructure_id, 'target_id' => $model->target_id], [
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
          'infrastructure_id',
          'infrastructure.name',
          'target_id',
          'target.fqdn',
        ],
    ]) ?>

</div>
