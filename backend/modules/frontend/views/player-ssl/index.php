<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\frontend\models\PlayerSslSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title='Player VPN Certificates';
$this->params['breadcrumbs'][]=$this->title;
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap\Modal::end();
?>
<div class="player-ssl-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Player VPN Certificate', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'serial',
            [
              'attribute' => 'player',
              'label'=>'Player',
              'value'=> function($model) {return sprintf("id:%d %s", $model->player_id, $model->player->username);},
            ],
            [
              'attribute' => 'subject',
              'label'=>'Subject',
              'value'=> function($model) {return $model->subjectString;},
            ],
            //'privkey:ntext',
            [
              'attribute'=>'ts',
              'format'=>'raw',
              'value'=>function($model){ return sprintf("%s / %s",$model->ts,Yii::$app->formatter->format($model->ts, 'relativeTime'));},
              'contentOptions' => ['class' => 'small'], // For TD

            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>


</div>
