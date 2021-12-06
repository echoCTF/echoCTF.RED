<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\PlayerFindingSearch */
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
<div class="player-finding-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Player Finding', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
          'player_id',
          [
            'attribute' => 'player',
            'label'=>'Player',
            'value'=> 'player.username',
          ],
          [
            'attribute' => 'target_id',
            'label'=>'Target ID',
            'value'=> 'finding.target_id',
          ],
          'finding_id',
          [
            'attribute' => 'finding',
            'label'=>'Finding',
            'value'=> 'finding.name',
          ],
          [
            'attribute' => 'points',
            'label'=>'Points',
            'value'=> 'points',
          ],
          'ts',

//            'player_id',
//            [
//                'attribute' => 'player',
//                'label'=>'Player',
//                'value'=> function($model) {return sprintf("id:%d %s", $model->player_id, $model->player->username);},
//            ],
//            'finding_id',
//            [
//              'attribute' => 'finding',
//              'label'=>'Finding',
//              'value'=> function($model) {return sprintf("id:%d %s", $model->finding_id, $model->finding->name);},
//            ],
//            'ts',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>


</div>
