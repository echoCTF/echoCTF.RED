<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\gameplay\models\TargetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id) . ' Statuses';
$this->params['breadcrumbs'][]=ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][]=['label' => 'Targets', 'url' => ['index']];
$this->params['breadcrumbs'][]=ucfirst(Yii::$app->controller->id) . ' Statuses';
?>
<div class="target-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
              'label' => 'name',
              'attribute' => 'name',
              'value' => function ($model) { return $model->getNames()[0];}
            ],
            [
              'label' => 'COMMAND',
              'attribute' => 'command',
              'value' => function ($model) { return $model->getCommand();}
            ],
            [
              'label' => 'Created',
              'attribute' => 'created',
              'value' => function ($model) { return Yii::$app->formatter->asRelativeTime($model->getCreated());}
            ],
            [
              'label' => 'State',
              'attribute' => 'state',
              'value' => function ($model) { return $model->getState();}
            ],
            [
              'label' => 'Status',
              'attribute' => 'status',
              'value' => function ($model) { return $model->getStatus();}
            ],
        ],
    ]);?>


</div>
