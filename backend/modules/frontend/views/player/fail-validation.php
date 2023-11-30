<?php

use yii\helpers\Html;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\frontend\models\PlayerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = ucfirst(Yii::$app->controller->module->id) . ' / ' . ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => 'Players', 'url' => ['index']];
yii\bootstrap5\Modal::begin([
  'title' => '<h2><i class="bi bi-info-circle-fill"></i> ' . Html::encode($this->title) . ' Help</h2>',
  'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/' . $this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="player-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a('Create Player', ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a('Import Players', ['import'], ['class' => 'btn btn-info']) ?>
    <?= Html::a(Yii::t('app', 'Fail Validate'), ['fail-validation'], [
      'class' => 'btn',
      'style' => 'background: #4d246f; color: white;',
      'data' => [
        'confirm' => Yii::t('app', 'This operation validates all the user details are you sure?'),
      ],
    ]) ?>
    <?= Html::a('Reset All player data', ['reset-playdata'], ['class' => 'btn btn-danger', 'data' => ['confirm' => 'Are you sure you want to delete all player data?', 'method' => 'post',]]) ?>
    <?= Html::a('Reset All player progress', ['reset-player-progress'], ['class' => 'btn btn-warning', 'data' => ['confirm' => 'Are you sure you want to delete all player progress?', 'method' => 'post',]]) ?>
  </p>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => false,
    'columns' => [
      [
        'attribute' => 'id',
        'headerOptions' => ['style' => 'width:4em'],
      ],

      [
        'attribute' => 'avatar',
        'format' => 'html',
        'value' => function ($model) {
          $data = clone $model;
          return Html::img('//' . Yii::$app->sys->offense_domain . '/images/avatars/' . $data->profile->avatar, ['width' => '50px']);
        }
      ],

      'username',
      [
        'attribute' => 'email',
        'format' => 'raw',
        'value' => function ($modelorig) {
          $model = clone $modelorig;
          $model->scenario = 'validator';
          if (!$model->validate('email')) {
            return Html::tag('b', $model->email);
          }
          return $model->email;
        }

      ],
      [
        'attribute' => 'verification_token',
        'format' => 'raw',
        'value' => function ($modelorig) {
          $model = clone $modelorig;
          $model->scenario = 'validator';
          if (!$model->validate('verification_token')) {
            return Html::a('Clear', ['/frontend/player/clear-verification-token', 'id' => $model->id], [
              'class' => 'btn text-center',
              'style' => 'background: #4d246f; color: white;',
              'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to clear the verification token of this player?'),
                'method' => 'post',
              ],
            ]);
          }
          return null;
        },
        'contentOptions' => ['style' => 'text-align: center'],

      ],
      [
        'attribute' => 'vpn_local_address',
        'label' => 'VPN Local IP',
        'value' => function ($model) {
          return $model->last && $model->last->vpn_local_address ? long2ip($model->last->vpn_local_address) : null;
        }
      ],
      'online:boolean',
      'active:boolean',
      [
        'attribute' => 'academic',
        'value' => 'academicShort',
        'filter' => [0 => Yii::$app->sys->academic_0short, 1 => Yii::$app->sys->academic_1short, 2 => Yii::$app->sys->academic_2short],
      ],
      [
        'attribute' => 'status',
      ],

      'created',
      //'ts',
      [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{player-view-full} {set-deleted} {view} {delete}',
        'buttons' => [
          'delete' => function ($url, $model) {
            return Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $model->id], [
              'class' => '',
              'data' => [
                'confirm' => 'Are you absolutely sure you want to delete [' . Html::encode($model->username) . '] ?',
                'method' => 'post',
              ],
            ]);
          },
          'set-deleted' => function ($url, $model) {
            return Html::a('<i class="bi bi-hammer"></i>', ['set-deleted', 'id' => $model->id], [
              'class' => '',
              'data' => [
                'confirm' => 'Are you absolutely sure you want to set status to deleted for [' . Html::encode($model->username) . '] ?',
                'method' => 'post',
              ],
            ]);
          },
          'player-view-full' => function ($url, $model) {
            $url =  \yii\helpers\Url::to(['/frontend/profile/view-full', 'id' => $model->profile->id]);
            return Html::a(
              '<i class="bi bi-person-lines-fill"></i>',
              $url,
              [
                'title' => 'View full profile',
                'data-pjax' => '0',
              ]
            );
          },

        ],
      ],
    ],
  ]); ?>


</div>