<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

/* @var $this yii\web\View */
/* @var $model app\modules\infrastructure\models\Server */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Servers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="server-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
<div class="row">
  <div class="col-lg-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'ipoctet',
            'network',
            'description:ntext',
            'service',
            'connstr',
            'ssl:boolean',
            'timeout',
            'provider_id',
        ],
    ]) ?>
  </div>
  <div class="col-lg-6">
      <?= GridView::widget([
        'dataProvider' => new ArrayDataProvider([
          'id' => 'teamInstances',
          'allModels' => $model->targetInstances,
          'sort' => [
            'sortParam' => 'teamInstance',
            'attributes' => ['player_id', 'target_id'],
          ],
          'pagination' => [
            'pageSize' => 20,
          ],
        ]),
        'columns' => [
          ['class' => 'app\components\columns\ProfileColumn', 'attribute' => 'player'],
          [
            'attribute' => 'target.name',
            'label' => 'Target'
          ],
          [
            'attribute' => 'ipoctet',
            'label' => 'IP'
          ],
          [
            'attribute' => 'reboot',
            'label' => 'Status',
            'value'=>'rebootVal'
          ],
          'created_at',
          'updated_at'
        ],
      ]); ?>

  </div>
</div>

</div>
