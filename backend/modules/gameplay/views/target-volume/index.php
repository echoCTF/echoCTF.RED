<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\gameplay\models\TargetVolumeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="target-volume-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Target Volume', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
              'attribute' => 'target',
              'label'=>'Target',
              'value'=> function($model) {return sprintf("(id:%d) %s/%s",$model->target_id,$model->target->name,$model->target->ipoctet);},
            ],
            'volume',
            'bind',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
