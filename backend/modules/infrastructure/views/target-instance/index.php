<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\infrastructure\models\TargetInstanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Target Instances');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="target-instance-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Target Instance'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
              'attribute'=>'player_id',
              'value'=>function($model){return $model->player_id.': '.$model->player->username;}
            ],
            [
              'attribute'=>'target_id',
              'value'=>function($model){return $model->target_id.': '.$model->target->name;}
            ],
            'server_id',
            'ip',
            'reboot',
            'created_at',
            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
