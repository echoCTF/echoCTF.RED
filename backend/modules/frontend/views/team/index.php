<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\frontend\models\TeamSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][]=['label' => 'Teams', 'url' => ['index']];
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap\Modal::end();
?>
<div class="team-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Team', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
              'attribute'=>'logo',
              'filter'=>false,
              'format'=>['image',['width'=>'40','height'=>'40']],
              'value'=>function($model){
                if($model->logo)
                  return '//'.Yii::$app->sys->offense_domain.'/images/avatars/team/'.$model->logo;
                return '//'.Yii::$app->sys->offense_domain.'/images/team_player.png';
              }
            ],
            [
              'attribute'=>'id',
              'headerOptions' => ['style' => 'width:50px'],
            ],
            'name',
            'description:ntext',
            [
              'attribute'=>'academic',
              'value'=>'ucacademicShort',
              'filter'=>[0=>'Gov',1=>'Edu', 2=>"Pro"],
            ],
            'inviteonly:boolean',
            [
              'label'=>'Owner',
              'attribute'=>'username',
              'format'=>'html',
              'value'=>function($model){ return Html::a($model->owner->username,['profile/view-full','id'=>$model->owner->profile->id]);}
            ],
            //'token',
            'ts',

            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{toggle-academic} {view} {update} {delete}',
              'buttons' => [
                  'toggle-academic' => function($url) {
                      return Html::a(
                          '<span class="glyphicon glyphicon glyphicon-education"></span>',
                          $url,
                          [
                              'title' => 'Toggle team academic flag',
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
