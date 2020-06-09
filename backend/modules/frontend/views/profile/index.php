<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\frontend\models\ProfileSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=Yii::t('app', 'Profiles');
$this->params['breadcrumbs'][]=$this->title;
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
            'avatar',
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
            'approved_avatar:boolean',
            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{view} {update} {delete} {approve_avatar}',
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
              ]
            ],
        ],
    ]);?>


</div>
