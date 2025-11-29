<?php

use app\modules\infrastructure\models\PrivateNetworkTarget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\modules\infrastructure\models\PrivateNetworkTargetSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Private Network Targets');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="private-network-target-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Private Network Target'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
              'attribute'=>'private_network_name',
              'value'=>'privateNetwork.name'
            ],
            [
              'attribute'=>'target_name',
              'value'=>'target.name'
            ],
            [
              'attribute'=>'server_name',
              'value'=>'server.name'
            ],
            'ipoctet',
            'state',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, PrivateNetworkTarget $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
