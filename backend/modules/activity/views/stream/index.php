<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\StreamSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="stream-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Stream', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
              'attribute'=>'formatted',
              'format'=>'html',
              'headerOptions' => ['style' => 'width:50%'],
            ],
/*            [
                'attribute' => 'player',
                'label'=>'Player',
                'value'=> function($model) {return sprintf("id:%d %s",$model->player_id,$model->player->username);},
            ],*/
            'player_id',
            'model',
            'model_id',
            'points',
//            'ts',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>


</div>
