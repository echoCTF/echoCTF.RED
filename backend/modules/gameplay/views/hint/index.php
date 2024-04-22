<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\gameplay\models\HintSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="hint-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Hint', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'title',
            'message:ntext',
            [
              'attribute'=>'badge_id',
              'value'=> function($model) {return $model->badge !== NULL ? sprintf("(id:%d) %s/%s", $model->badge_id, $model->badge->name, $model->badge->points) : "";} ,
            ],
            [
              'attribute'=>'finding_id',
              'value'=> function($model) {return $model->finding !== NULL ? sprintf("(id:%d) %s/%s", $model->finding_id, $model->finding->name, $model->finding->points) : "";} ,
            ],
            [
              'attribute'=>'treasure_id',
              'value'=> function($model) {return $model->treasure !== NULL ? sprintf("(id:%d) %s/%s", $model->treasure_id, $model->treasure->name, $model->treasure->points) : "";} ,
            ],
            [
              'attribute'=>'question_id',
              'value'=> function($model) {return $model->question !== NULL ? sprintf("(id:%d) %s/%s", $model->question_id, $model->question->name, $model->question->points) : "";} ,
            ],
            //'points_user',
            //'points_team',
            //'timeafter:datetime',
            [
              'attribute'=>'player_type',
              'filter'=>['offense'=>'Offense', 'defense'=>'Defense', 'both'=>'Both'],
            ],
            'active:boolean',
            //'ts',

            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{give} {view} {update} {delete}',
              'buttons' => [
                  'give' => function($url) {
                      return Html::a(
                          '<i class="bi bi-send-exclamation"></i>',
                          $url,
                          [
                              'title' => 'Give this hint to all active users',
                              'data-pjax' => '0',
                              'data-method' => 'POST',
                          ]
                      );
                  },
              ],
            ],

        ],
    ]);?>


</div>
