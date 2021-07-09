<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\TargetMetadata */

$this->title = $model->target_id;
$this->params['breadcrumbs'][] = ['label' => 'Infrastructure', 'url' => ['/infrastructure/default/index']];
$this->params['breadcrumbs'][] = ['label' => 'Target metadata', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="target-metadata-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->target_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->target_id], [
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
            'target_id',
            'target.fqdn',
            'scenario:ntext',
            'instructions:ntext',
            'solution:ntext',
            'pre_credits:ntext',
            'post_credits:ntext',
            'pre_exploitation:ntext',
            'post_exploitation:ntext',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
