<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\PlayerVpnHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Player Vpn Histories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-vpn-history-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Truncate Player Vpn History'), ['truncate'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'player_id',
            [
              'attribute'=>'username',
              'value'=>'player.username',
            ],
            [
              'attribute'=>'vpn_remote_address',
              'value'=>function($model){return long2ip($model->vpn_remote_address);},
            ],
            [
              'attribute'=>'vpn_local_address',
              'value'=>function($model){return long2ip($model->vpn_local_address);},
            ],
            'ts',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
