<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\frontend\models\ProfileSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=Yii::t('app', 'Failed validation Profiles');
$this->params['breadcrumbs'][]=['label' => Yii::t('app', 'Profiles'), 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="profile-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Profiles'), ['index'], ['class' => 'btn btn-info']) ?>
        <?= Html::a(Yii::t('app', 'Create Profile'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Clear All Fail Validate'), ['clear-all-validation'], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to clear all the validation failures?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => false,
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
            'twitter',
            'github',
            'discord',
            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{view} {update} {delete} {clear-validation} {player-view} {player-view-full}',
              'buttons' => [
                  'clear-validation' => function($url, $modelorig) {
                    $model=clone $modelorig;
                    $model->scenario='validator';
                    if(!$model->validate())
                    return Html::a(
                        '<i class="bi bi-check-circle"></i>',
                        $url,
                        [
                          'title' => 'Clear failed validation fields',
                          'data-pjax' => '0',
                          'data-method' => 'POST',
                          'data'=>['confirm'=>"Are you sure you want to clear the fields that fail validation?"]
                        ]
                    );
                  },
                  'player-view' => function($url, $model) {
                    $url =  \yii\helpers\Url::to(['player/view', 'id' => $model->player_id]);
                    return Html::a(
                        '<i class="far fa-user"></i>',
                        $url,
                        [
                          'title' => 'View player',
                          'data-pjax' => '0',
                        ]
                    );
                  },
                  'player-view-full' => function($url, $model) {
                    $url =  \yii\helpers\Url::to(['view-full', 'id' => $model->id]);
                    return Html::a(
                        '<i class="bi bi-person-lines-fill"></i>',
                        $url,
                        [
                          'title' => 'View full profile',
                          'data-pjax' => '0',
                        ]
                    );
                  },
              ]
            ],
        ],
    ]);?>


</div>
