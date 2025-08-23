<?php
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use \app\modules\frontend\models\Player;
use yii\helpers\Html;

$dataProvider = new ArrayDataProvider([
    'allModels' => $spammy,
    'pagination' => false,
]);

?>
<div class="spammy-domains">

<?php

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'header' => 'players',
            'format'=>'raw',
            'value' => function($model, $key, $index, $column) {
              $links=[];
              $filter=sprintf("email like '%%%s'",$key);
              foreach(Player::find()->where($filter)->all() as $player)
              {
                $links[]=Html::a($player->username, ['/frontend/profile/view-full', 'id' => $player->profile->id]). " ".
                Html::a('<i class="fa fa-trash"></i>', ['/frontend/player/delete', 'id' => $player->id],[
                  'data' => [
                    'confirm'=>'Are you sure you want to delete ['.$player->username.']?',
                    'method' => 'post'
                    ]
                ]);
              }
              return $links===[] ? "" : implode(', ',$links);
            },
        ],
        [
            'header' => 'domain',
            'value' => function($model, $key, $index, $column) {
                return $key;  // $key is the array key for the current row
            },
        ],
        [
            'header' => 'errors',
            'format'=>'html',
            'value' => function($model, $key, $index, $column) {
                unset($model['players']);
                return '<small><code>'.implode('.<br/>',$model).'</code></small>';
            },
        ],

    ],
]);
?>
</div>