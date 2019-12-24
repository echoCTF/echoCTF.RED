<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\gameplay\models\FindingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = $this->title;
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
              'filter'=>['tcp'=>'TCP', 'udp'=>'UDP','icmp'=>'ICMP'],
            ],
            'target_id',
            [
                'attribute' => 'ipoctet',
                'label'=>'Target',
                'value'=> function($model) {return sprintf("(id:%d) %s/%s",$model->target_id,$model->target->name,$model->target->ipoctet);},
            ],
            'port',
            'stock',
            [
              'attribute'=>'discovered',
              'value'=>function($model) {return count($model->playerFindings);},
              'filter'=>[0=>'No',1=>'Yes'],
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
