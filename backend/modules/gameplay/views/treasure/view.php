<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Treasure */

$this->title=$model->name;
$this->params['breadcrumbs'][]=ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][]=['label' => 'Treasures', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="treasure-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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
            'id',
            [
                'label'=>'Target',
                'value'=>sprintf("(id:%d) %s/%s", $model->target_id, $model->target->name, $model->target->ipoctet),
            ],
            'name',
            'pubname',
            'category',
            'description:ntext',
            'pubdescription:ntext',
            'points',
            'player_type',
            'csum',
            'appears',
            'effects',
            'code',
            'location',
            'suggestion',
            'solution',
            'weight',
            'ts',
        ],
    ]) ?>

</div>
