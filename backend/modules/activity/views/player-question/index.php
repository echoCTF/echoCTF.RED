<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\PlayerQuestionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="player-question-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Player Question', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'question_id',
            [
              'attribute' => 'challenge_id',
              'label'=>'Challenge ID',
              'value'=>function($model) {return $model->question->challenge_id;},
            ],
            [
                'attribute' => 'question',
                'label'=>'Question',
                'value'=> function($model) {return sprintf("%s", Html::encode($model->question->name));},
            ],
            'player_id',
            [
                'attribute' => 'player',
                'label'=>'Player',
                'value'=> function($model) {return sprintf("%s", Html::encode($model->player->username));},
            ],
            'points',
            'ts',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>


</div>
