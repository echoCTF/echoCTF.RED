<?php

use app\modules\frontend\models\PlayerToken;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\modules\frontend\models\PlayerTokenSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Player Tokens');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-token-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Player Token'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            ['class' => 'app\components\columns\ProfileColumn',  'idkey' => 'player.profile.id', 'attribute' => 'username', 'field' => 'player.username'],
            'type',
            'token',
            'description',
            'expires_at',
            'created_at',
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, PlayerToken $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'player_id' => $model->player_id, 'type' => $model->type]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
