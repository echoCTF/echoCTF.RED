<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\sales\models\PlayerCustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Player Customers');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['/sales/default/index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>
<div class="player-customer-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
      <?= Html::a(Yii::t('app', 'Fetch from Stripe'), ['fetch-stripe'], ['class' => 'btn btn-warning']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            ['class' => 'app\components\columns\ProfileColumn','attribute'=>'username','idkey'=>'profile.id','field'=>'username'],
            'fullname',
            'email',
            [
                'attribute'=>'stripe_customer_id',
                'format'=>'raw',
                'value'=>function($model){
                    if($model->stripe_customer_id)
                        return Html::a($model->stripe_customer_id,"https://dashboard.stripe.com/customers/".$model->stripe_customer_id,['target'=>'_blank']);
                    return "";
                }
            ],
            'created',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
