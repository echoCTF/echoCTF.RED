<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\gameplay\models\NetworkPlayerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=Yii::t('app', 'Network Players');
$this->params['breadcrumbs'][]=$this->title;
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/index.md'), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="network-player-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Network Player'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'network_id',
            [
                'attribute'=>'network_name',
                'value'=>'network.name',
            ],
            'player_id',
            ['class' => 'app\components\columns\ProfileColumn'],
            'created_at',
            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>


</div>
