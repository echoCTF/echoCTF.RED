<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\PlayerCounterNfSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Player Counter Nfs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-counter-nf-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Player Counter Nf'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'player_id',
            [
                'attribute'=>'username',
                'format'=>'html',
                'value'=> function($modelorig) {$model=$modelorig->player; return Html::a($model->username,['/frontend/profile/view-full','id'=>$model->profile->id],['class' => 'profile-link','title'=>\Yii::t('app','Go to profile of [{username}]',['username'=>$model->username])]);},
            ],
            [
                'attribute'=>'metric',
                'filter'=>$searchModel->distinctMetrics(),
            ],
            'counter',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
