<?php

use app\modules\moderation\models\Abuser;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\modules\frontend\models\AbuserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Abusers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="abuser-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Abuser'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute'=>'id',
                'headerOptions' => ['style' => 'width:4em'],
            ],
            [
                'attribute' => 'player_id',
                'headerOptions' => ['style' => 'width:4em'],
            ],
            ['class' => 'app\components\columns\ProfileColumn', 'idkey' => 'player.profile.id', 'attribute' => 'username', 'field' => 'player.username'],
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
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
