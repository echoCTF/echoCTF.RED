<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\settings\models\ExperienceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=Yii::t('app', 'Experience Levels');
$this->params['breadcrumbs'][]=$this->title;
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/index.md'), 'gfm');
yii\bootstrap\Modal::end();
?>
<div class="experience-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Experience'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'name',
            'category',
            'description:ntext',
            'icon',
            'min_points',
            'max_points',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>


</div>
