<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\TeamScoreSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title='Team Scores';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="team-score-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Team Score', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Export Top15 CSV', ['top15'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('Export Top15 w/ players CSV', ['top15-inclusive'], ['class' => 'btn btn-info']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'team_id',
            [
              'attribute' => 'team_name',
              'value' => 'team.name',
              'label'=>'Team',
              //'value'=> function($model) {return sprintf("id:%d %s", $model->team_id, $model->team->name);},
            ],
            [
                'attribute' => 'team_academic',
                'label'=>'Academic',
                'value'=>'team.academicShort',
                'filter'=>[0=>'Gov',1=>'Edu', 2=>"Pro"],
            ],
            'points',
            'ts',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>


</div>
