<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\frontend\models\TeamPlayerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][]=$this->title;
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.$this->title.' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap\Modal::end();
?>
<div class="team-player-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Team Player', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            [
              'attribute' => 'player',
              'label'=>'Player',
              'value'=> function($model) {return sprintf("id:%d %s", $model->player_id, $model->player->username);},
            ],
            [
              'attribute' => 'team',
              'label'=>'Team',
              'value'=> function($model) {return sprintf("id:%d %s", $model->team_id, $model->team->name);},
            ],
            'approved:boolean',
            'ts',
            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{toggle-approved} '.'{view} {update} {delete}',
              'buttons' => [
                'toggle-approved' => function($url) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-ok"></span>',
                        $url,
                        [
                            'title' => 'Toggle membership approved flag',
                            'data-pjax' => '0',
                            'data-method' => 'POST',
                            'data'=>['confirm'=>'Are you sure you want to toggle the approved flag for this user?']

                        ]
                    );
                },
              ]

            ],
        ],
    ]);?>


</div>
