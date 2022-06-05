<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\sales\models\PlayerSubscriptionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Player Subscriptions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-subscription-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Player Subscription'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Fetch from Stripe'), ['fetch-stripe'], ['class' => 'btn btn-warning']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'player_id',
            [
              'attribute'=>'username',
              'value'=>'player.username'
            ],
            [
              'attribute'=>'subscription_id',
              'format'=>'raw',
              'value'=>function($model){ return '<small>'.substr($model->subscription_id,0,25).'</small>';}
            ],
  //          [
  //            'attribute'=>'session_id',
  //            'format'=>'raw',
  //            'value'=>function($model){ return '<small>'.substr($model->session_id,0,25).'</small>';}
  //          ],
            [
              'attribute'=>'price_id',
              'format'=>'raw',
              'value'=>function($model){ return '<small>'.substr($model->price_id,0,25).'</small>';}
            ],
            'active:boolean',
            'starting',
            'ending',
//            'created_at',
//            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
