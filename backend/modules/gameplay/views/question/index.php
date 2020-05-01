<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\gameplay\models\QuestionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="question-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Question', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'challenge_id',
            [
              'attribute' => 'challengename',
              'label'=>'Challenge Name',
              'value'=> function($model) {return sprintf("%s", $model->challenge->name);},
            ],
            'name',
            'description:ntext',
            'points',
            //'player_type',
            'code',
            //'weight',
            [
              'attribute'=>'answered',
              'value'=>function($model) {return count($model->playerQuestions);},
              'filter'=>[0=>'No', 1=>'Yes'],
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>


</div>
