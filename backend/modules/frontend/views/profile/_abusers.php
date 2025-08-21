<?php

use yii\grid\ActionColumn;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\modules\moderation\models\Abuser;
?>
<?php Pjax::begin(['id' => 'notificationsPJ', 'enablePushState' => false, 'enableReplaceState' => false,]); ?>
<h5>Abusers Entries</h5>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute'=>'id',
                'headerOptions' => ['style' => 'width:4em'],
            ],
            [
                'attribute'=>'title',
                'headerOptions' => ['style' => 'width:18em'],
            ],
            'reason',
            'model',
            'model_id',
            'created_at',
            'updated_at',
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Abuser $model, $key, $index, $column) {
                    return Url::toRoute(["/moderation/abuser/$action", 'id' => $model->id]);
                 }
            ],
        ],
    ]);
Pjax::end();
