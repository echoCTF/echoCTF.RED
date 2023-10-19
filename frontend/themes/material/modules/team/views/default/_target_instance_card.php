<?php

use yii\helpers\Html;
use app\widgets\Card;

$target = $model->target;
$display_ip = long2ip($model->ip);
Card::begin([
  'header' => 'header-icon',
  'type' => 'card-stats bg-dark',
  'encode' => false,
  'icon' => sprintf('<img src="%s" class="img-fluid" style="max-width: 10rem; max-height: 4rem;" />', $target->logo),
  'color' => 'target',
  'subtitle' => '<small>owner: '.Html::encode($model->player->username).'</small>',
  'title' => Html::a(sprintf('%s / %s', $target->name, $display_ip), ['/target/default/versus', 'id' => $target->id,'profile_id'=>$model->player->profile->id], ['class' => 'text-primary', 'style' => 'font-size: 0.9em;']),
  'footer' => false,
]);

Card::end();