<?php

use yii\grid\ActionColumn;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\modules\moderation\models\Abuser;
?>
<?php Pjax::begin(['id' => 'abusersPJ', 'enablePushState' => false, 'enableReplaceState' => false,]); ?>
<h5>Abusers Entries <?= Html::a(Yii::t('app', 'Create Abuser'), ['/moderation/abuser/create','player_id'=>$model->owner->id], ['class' => 'btn btn-success']) ?></h5>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'app\components\columns\ProfileColumn', 'idkey' => 'player.profile.id', 'attribute' => 'username', 'field' => 'player.username'],
            [
              'label'=>'Formatted',
              'format'=>'html',
              'value'=>function ($model) { return $model->formatted; }
            ],
            [
                'class' => ActionColumn::class,
                'template'=>'{delete}',
                'urlCreator' => function ($action, Abuser $model, $key, $index, $column) {
                    return Url::toRoute(["/moderation/abuser/$action", 'id' => $model->id]);
                 }
            ],
        ],
    ]);
Pjax::end();
