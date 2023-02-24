<?php

use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
?>
<?php Pjax::begin(['id' => 'solvesPJ','enablePushState'=>false,'enableReplaceState'=>false,]); ?>
<h5>Challenge Solves</h5>
<?= GridView::widget([
    'id' => 'solves',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'challenge_name',
            'value' => function ($model) {
                return sprintf("ID: %d / %s", $model->challenge_id, $model->challenge->name);
            },
            'headerOptions' => ['style' => 'width:20vw'],
        ],
        'timer',
        'rating',
        'first:boolean',
        'created_at:datetime',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
            'urlCreator' => function ($action, $model, $key, $index) {
                return Url::to(['/activity/challenge-solver/' . $action, 'player_id' => $model->player_id, 'challenge_id' => $model->challenge_id]);
            },
            'buttons' => [
                'approve' => function ($url) {
                    return Html::a(
                        '<i class="bi bi-check-circle-fill"></i>',
                        $url,
                        [
                            'title' => 'Approve writeup',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]
                    );
                },
            ],
        ],
    ],
]);
Pjax::end();
