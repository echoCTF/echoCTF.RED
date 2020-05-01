<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\gameplay\models\TreasureActionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title='Treasure Actions';
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="treasure-action-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Treasure Action', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'treasure.name',
            'ipoctet',
            'port',
            'command:ntext',
            'weight',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>


</div>
