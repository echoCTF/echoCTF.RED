<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\sales\models\PlayerCustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Player Customers');
$this->params['breadcrumbs'][] = $this->title;
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
            'username',
            'fullname',
            'email',
            'stripe_customer_id',
            'created',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
