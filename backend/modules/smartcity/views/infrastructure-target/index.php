<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\gameplay\models\InfrastructureTargetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title='Infrastructure Targets';
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="infrastructure-target-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Infrastructure Target', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'infrastructure_id',
            'infrastructure.name',
            'target_id',
            'target.fqdn',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>


</div>
