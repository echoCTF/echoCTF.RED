<?php

use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use \app\modules\frontend\models\Player;
use yii\helpers\Html;

$dataProvider = new ArrayDataProvider([
  'allModels' => $spammy,
  'pagination' => false,
]);
$this->title = ucfirst(Yii::$app->controller->module->id) . ' / ' . ucfirst(Yii::$app->controller->id) . ' / Check Spammy';
$this->params['breadcrumbs'][] = ['label' => ucfirst(Yii::$app->controller->module->id)];
$this->params['breadcrumbs'][] = ['label' => ucfirst(Yii::$app->controller->id), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Check Spammy', 'url' => ['check-spammy']];

?>
<div class="spammy-domains">
  <h1>Spammy Domains</h1>
  <p>Email domains that fail validation</p>

  <?php

  echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
      [
        'header' => 'Players',
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column) {
          $links = [];
          $filter = sprintf("email like '%%%s'", $key);
          foreach (Player::find()->where($filter)->all() as $player) {
            $links[] = Html::a($player->username, ['/frontend/profile/view-full', 'id' => $player->profile->id]) . " " .
              Html::a('<i class="fa fa-trash"></i>', ['/frontend/player/delete', 'id' => $player->id], [
                'data' => [
                  'confirm' => 'Are you sure you want to delete [' . $player->username . ']?',
                  'method' => 'post'
                ]
              ]);
          }
          return $links === [] ? "" : implode(', ', $links);
        },
      ],
      [
        'header' => 'Domain',
        'value' => function ($model, $key, $index, $column) {
          return $key;  // $key is the array key for the current row
        },
      ],
      [
        'header' => 'Error',
        'format' => 'html',
        'value' => function ($model, $key, $index, $column) {
          unset($model['players']);
          return '<small><code>' . implode('.<br/>', $model) . '</code></small>';
        },
      ],
      [
        'class' => 'yii\grid\ActionColumn',
        'header' => 'Actions',
        'template' => "{notify} {delete}",
        'buttons' => [
          'delete' => function ($url, $model,$key) {
            return Html::a('<i class="fas fa-users-slash"></i>', ['/frontend/player/delete-domain', 'domain' => $key], [
              'class' => '',
              'title' => 'Delete players from this domain ['.$key.']?',
              'data' => [
                'confirm' => 'Are you absolutely sure you want to delete the players from the domain [' . Html::encode($key) . '] ?',
                'method' => 'post',
              ],
            ]);
          },
          'notify' => function ($url, $model,$key) {
            return Html::a('<i class="far fa-paper-plane"></i>', ['notify-spammy', 'domain' => $key], [
              'class' => '',
              'title' => 'Notify spammy players for domain ['.$key.'] to change their email?',
              'data' => [
                'confirm' => 'Are you absolutely sure you want to notify spammy players for domain ['.Html::encode($key).'] to change their email?',
                'method' => 'post',
              ],
            ]);
          },
        ],
      ]
    ],
  ]);
  ?>
</div>