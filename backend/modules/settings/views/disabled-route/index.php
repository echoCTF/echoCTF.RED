<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\settings\models\DisabledRouteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Disabled Routes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="disabled-route-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Disabled Route', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'route',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
