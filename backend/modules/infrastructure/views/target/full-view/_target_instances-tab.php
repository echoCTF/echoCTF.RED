<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\modules\infrastructure\models\Server;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\infrastructure\models\TargetInstanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

Pjax::begin(['id' => 'instances-tabPJ', 'enablePushState' => false, 'enableReplaceState' => false,]); ?>
<h3>Target Instances</h3>

<?= GridView::widget([
  'id' => 'instances-tab',
  'dataProvider' => $dataProvider,
  'columns' => [
    [
      'attribute' => 'player_id',
      'value' => function ($model) {
        return $model->player_id . ': ' . $model->player->username;
      }
    ],
    [
      'attribute' => 'server_id',
      'filter' => ArrayHelper::map(Server::find()->orderBy(['name' => SORT_ASC, 'ip' => SORT_ASC])->asArray()->all(), 'id', 'name'),
      'value' => function ($model) {
        if ($model->server) return $model->server_id . ': ' . $model->server->name;
        return null;
      }
    ],
    'ipoctet',
    [
      'attribute' => 'reboot',
      'value' => 'rebootVal',
      'filter' => [0 => 'Start / Do Nothing', 1 => 'Restart', 2 => 'Destroy'],
      'headerOptions' => ['style' => 'width:7em'],
      'contentOptions' => ['style' => 'white-space: nowrap;'],
    ],
    'team_allowed:boolean',
    [
      'class' => 'yii\grid\ActionColumn',
      'template' => '{restart} {destroy} {view} {update} {delete}',
      'contentOptions' => ['style' => 'white-space: nowrap;'],
      'buttons' => [
        'restart' => function ($url) {
          return Html::a(
            '<i class="bi bi-arrow-clockwise"></i>',
            $url,
            [
              'title' => 'Restart instance',
              'data-pjax' => '0',
              'data-confirm' => 'You are about to start/restart this instance. Are you sure?',
              'data-method' => 'POST',
            ]
          );
        },
        'destroy' => function ($url) {
          return Html::a(
            '<i class="bi bi-power"></i>',
            $url,
            [
              'title' => 'Destroy container',
              'data-pjax' => '0',
              'data-confirm' => 'You are about to destroy this instance. Are you sure?',
              'data-method' => 'POST',
            ]
          );
        },
      ],
    ],
  ],
]);
Pjax::end();
