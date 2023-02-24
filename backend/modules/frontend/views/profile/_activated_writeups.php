<?php

use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
?>
<?php Pjax::begin(['id' => 'activated-writeupsPJ', 'enablePushState' => false, 'enableReplaceState' => false,]); ?>
<h5>Activated Writeups</h5>
<?= GridView::widget([
    'id' => 'activated-writeups',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'target_id',
            'headerOptions' => ['style' => 'width:3vw'],
        ],
        [
            'attribute' => 'target_name',
            'value' => 'target.name',
        ],
        'created_at',
    ],
]);
Pjax::end();
