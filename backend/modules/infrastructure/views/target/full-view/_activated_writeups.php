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
            'attribute' => 'player_id',
            'headerOptions' => ['style' => 'width:3vw'],
        ],
        ['class' => 'app\components\columns\ProfileColumn','attribute'=>'username','label'=>'Username','idkey'=>'player.profile.id','field'=>'player.username'],
        'created_at',
    ],
]);
Pjax::end();
