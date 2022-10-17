<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\TeamScoreSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title='Team Scores';
$this->params['breadcrumbs'][]=$this->title;
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.$this->title.' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap\Modal::end();
?>
<div class="team-score-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Team Score', ['create'], ['class' => 'btn btn-success']) ?>
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
