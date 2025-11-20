<?php

use app\modules\sales\models\PlayerPaymentHistory;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\modules\sales\models\PlayerPaymentHistorySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Player Payment Histories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-payment-history-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'payment_id',
            [
              'attribute'=>'username',
              'value'=>'player.username',
            ],
            [
              'attribute'=>'amount',
              'format'=>'currency',
              'value'=>function($model){return $model->amount/100;}
            ],
            'created_at',
            [
                'class' => ActionColumn::className(),
                'template'=>'{view} {delete}',
                'urlCreator' => function ($action, PlayerPaymentHistory $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
