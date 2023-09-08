<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\frontend\models\ProfileSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Profiles');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
yii\bootstrap5\Modal::begin([
  'title' => '<h2><i class="bi bi-info-circle-fill"></i> ' . Html::encode($this->title) . ' Help</h2>',
  'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/' . $this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="profile-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a(Yii::t('app', 'Create Profile'), ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a(Yii::t('app', 'Fail Validate'), ['fail-validation'], [
      'class' => 'btn',
      'style' => 'background: #4d246f; color: white;',
      'data' => [
        'confirm' => Yii::t('app', 'This operation validates all the user profiles are you sure?'),
      ],
    ]) ?>
  </p>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'rowOptions' => function ($model, $key, $index, $grid) {
      $model->scenario = 'validator';
      if (!$model->validate()) {
        return ['style' => 'font-weight: 300; background: #ffcccb'];
      }
      return [];
    },
    'columns' => [
      'id',
      'player_id',
      [
        'attribute' => 'avatar',
        'format' => ['image', ['width' => '40px', 'class' => 'img-thumbnail']],
        'contentOptions' => function ($model, $key, $index, $column) {
          return ['class' => 'text-center' . ($model->approved_avatar == 1 ? ' approved_avatar' : ' pending_avatar')];
        },
        'value' => function ($data) {
          return '//' . Yii::$app->sys->offense_domain . '/images/avatars/' . $data['avatar'];
        }
      ],
      [
        'attribute' => 'username',
        'label' => 'Username',
        'value' => 'owner.username'
      ],
      'bio:ntext',
      'country',
      [
        'attribute' => 'visibility',
        'filter' => $searchModel->visibilities
      ],
      'twitter',
      'github',
      'discord',
      'echoctf',
      [
        'attribute' => 'approved_avatar',
        'format' => 'boolean',
        'visible' => Yii::$app->sys->approved_avatar === false,
      ],
      [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view} {update} {delete} {approve-avatar} {clear-validation} {player-view} {player-view-full}',
        'buttons' => [
          'approve-avatar' => function ($url, $model) {
            if (!$model->approved_avatar)
              return Html::a(
                '<i class="bi bi-file-image-fill"></i>',
                $url,
                [
                  'title' => 'Approve avatar for the user',
                  'data-pjax' => '0',
                  'data-method' => 'POST',
                  'data' => ['confirm' => "Are you sure you want to approve the user avatar?"]
                ]
              );
          },
          'clear-validation' => function ($url, $model) {
            $model->scenario = 'validator';
            if (!$model->validate())
              return Html::a(
                '<i class="bi bi-check-circle"></i>',
                $url,
                [
                  'title' => 'Clear failed validation fields',
                  'data-pjax' => '0',
                  'data-method' => 'POST',
                  'data' => ['confirm' => "Are you sure you want to clear the fields that fail validation?"]
                ]
              );
          },
          'player-view' => function ($url, $model) {
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
          'player-view-full' => function ($url, $model) {
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
  ]); ?>

</div>