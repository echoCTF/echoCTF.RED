<?php
use yii\widgets\ListView;
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\Twitter;
?>
<div class="challenge-index">
  <div class="body-content">
    <h2><?=$TITLE?></h2>
      <?=$CATEGORY?>
    <?=
    ListView::widget([
        'options'=>['class'=>'list-view row'],
        'itemOptions' => [
          'class'=>'col col-sm-4 d-flex align-items-stretch'
        ],
        'dataProvider' => $dataProvider,
        'layout'=>"{items}",
        'summary'=>false,
        'pager'=>false,
        'itemView' => '_target_card',
        'viewParams'=>['identity'=>Yii::$app->user->identity->profile]
    ]);
    ?>
  </div>
</div>
