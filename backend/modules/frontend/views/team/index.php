<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\frontend\models\TeamSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][]=['label' => 'Teams', 'url' => ['index']];
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
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
              'attribute'=>'id',
              'headerOptions' => ['style' => 'width:50px'],
            ],
            [
              'attribute'=>'logo',
              'filter'=>false,
              'format'=>['image',['width'=>'40','height'=>'40', 'class'=>'rounded-circle bg-dark shadow']],
              'value'=>function($model){
                if($model->logo)
                  return '//'.Yii::$app->sys->offense_domain.'/images/avatars/team/'.$model->logo;
                return '//'.Yii::$app->sys->offense_domain.'/images/team_player.png';
              }
            ],
            'name',
            ['class' => 'app\components\columns\ProfileColumn','attribute'=>'username','label'=>'Owner','idkey'=>'owner.profile.id','field'=>'owner.username'],

            'description:ntext',
            [
              'attribute'=>'academic',
              'value'=>'ucacademicShort',
              'filter'=>[0=>'Gov',1=>'Edu', 2=>"Pro"],
            ],
            'inviteonly:boolean',
            'locked:boolean',
            //'token',
            [
              'label'=>'Members',
              'attribute'=>'team_members',
              'format'=>'integer',
              'value'=>function($model){ return count($model->players); },
              'contentOptions'=>['class'=>'text-center'],
            ],
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
