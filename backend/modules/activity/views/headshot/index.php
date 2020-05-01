<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\HeadshotSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=Yii::t('app', 'Headshots');
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="headshot-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Headshot'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin();?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'player_id',
            [
              'attribute'=>'username',
              'value'=>'player.username',
            ],
            'target_id',
            [
              'attribute'=>'fqdn',
              'value'=>'target.fqdn',
            ],
            [
              'attribute'=>'ipoctet',
              'value'=>'target.ipoctet',
            ],
            'timer',
            'rating',
            'created_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>

    <?php Pjax::end();?>

</div>
