<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\infrastructure\models\TargetMetadataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Target Metadatas';
$this->params['breadcrumbs'][] = ['label' => 'Infrastructure', 'url' => ['/infrastructure/default/index']];
$this->params['breadcrumbs'][] = ['label' => 'Target metadata', 'url' => ['index']];
?>
<div class="target-metadata-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Target Metadata', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'target_id',
            [
              'attribute'=>'fqdn',
              'value'=>'target.fqdn',
            ],
            'scenario:ntext',
            'instructions:ntext',
            'solution:ntext',
            'pre_credits:ntext',
            //'post_credits:ntext',
            //'pre_exploitation:ntext',
            //'post_exploitation:ntext',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
