<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\gameplay\models\FindingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="finding-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Finding', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'name',
            'pubname',
//            'description:ntext',
//            'pubdescription:ntext',
            'points',
            [
              'attribute'=>'protocol',
              'filter'=>['tcp'=>'TCP', 'udp'=>'UDP', 'icmp'=>'ICMP'],
            ],
            'target_id',
            [
                'attribute' => 'ipoctet',
                'label'=>'Target',
                'value'=> function($model) {return sprintf("(id:%d) %s/%s", $model->target_id, $model->target->name, $model->target->ipoctet);},
            ],
            'port',
            'stock',
            [
              'attribute'=>'discovered',
              'value'=>function($model) {return count($model->playerFindings);},
              'filter'=>[0=>'No', 1=>'Yes'],
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>


</div>
