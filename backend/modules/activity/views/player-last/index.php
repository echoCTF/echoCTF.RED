<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\PlayerLastSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Players Last activity');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-last-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
              'attribute'=>'username',
              'value'=>'player.username'
            ],
            [
              'attribute'=>'on_pui',
              'value'=>function($model){ return $model->on_pui==0 ? null : $model->on_pui; }
            ],
            [
              'attribute'=>'on_vpn',
              'value'=>function($model){return $model->on_vpn==0 ? null : $model->on_vpn;}
            ],
            [
              'attribute'=>'vpn_remote_address',
              'value'=>function($model){return $model->vpn_remote_address===NULL ? null : long2ip($model->vpn_remote_address);},
            ],
            [
              'attribute'=>'vpn_local_address',
              'value'=>function($model){return $model->vpn_local_address===null ? null : long2ip($model->vpn_local_address);},
            ],
            'ts',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
