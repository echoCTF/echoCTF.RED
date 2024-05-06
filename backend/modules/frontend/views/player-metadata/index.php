<?php

use app\modules\frontend\models\PlayerMetadata;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\modules\frontend\models\PlayerMetadataSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Player Metadata');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-metadata-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Player Metadata'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'player_id',
            ['class' => 'app\components\columns\ProfileColumn','attribute'=>'username'],
            'identificationFile',
            'affiliation',
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, PlayerMetadata $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'player_id' => $model->player_id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
