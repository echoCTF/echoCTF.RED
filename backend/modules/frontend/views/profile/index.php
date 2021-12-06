<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\frontend\models\ProfileSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=Yii::t('app', 'Profiles');
$this->params['breadcrumbs'][]=$this->title;
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.$this->title.' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap\Modal::end();
?>
<div class="profile-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Profile'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'player_id',
            [
              'attribute'=>'username',
              'label'=>'Username',
              'value'=>function($model) {return sprintf("%d: %s", $model->player_id, $model->owner->username);}
            ],
            'bio:ntext',
            'country',
            [
              'attribute'=>'visibility',
              'filter'=>$searchModel->visibilities
            ],
            'twitter',
            'github',
            'discord',
//            'terms_and_conditions:boolean',
//            'mail_optin:boolean',
//            'gdpr:boolean',
            [
              'attribute'=>'avatar',
              'format'=>'html',
              'value'=>function($data) { return Html::img('https://'.Yii::$app->sys->offense_domain.'/images/avatars/' . $data['avatar'],['width' => '50px']);}
            ],
            'approved_avatar:boolean',
            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{view} {update} {delete} {approve_avatar} {player-view}',
              'buttons' => [
                  'approve_avatar' => function($url, $model) {
                    if(!$model->approved_avatar)
                    return Html::a(
                        '<span class="glyphicon glyphicon-file"></span>',
                        $url,
                        [
                          'title' => 'Approve avatar for the user',
                          'data-pjax' => '0',
                          'data-method' => 'POST',
                          'data'=>['confirm'=>"Are you sure you want to approve the user avatar?"]
                        ]
                    );
                  },
                  'player-view' => function($url, $model) {
                    $url =  \yii\helpers\Url::to(['player/view', 'id' => $model->player_id]);
                    return Html::a(
                        '<span class="glyphicon glyphicon-user"></span>',
                        $url,
                        [
                          'title' => 'View player',
                          'data-pjax' => '0',
                        ]
                    );
                  },
              ]
            ],
        ],
    ]);?>


</div>
