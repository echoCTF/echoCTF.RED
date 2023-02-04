<?php

use yii\helpers\Html;
use app\widgets\Card;

$target = Yii::$app->user->identity->instance->target;
$target_ip = long2ip(Yii::$app->user->identity->instance->ip);
if (Yii::$app->user->identity->instance->ip === null)
  $display_ip = Html::tag('b', $target_ip, ["class" => 'text-danger', 'data-toggle' => 'tooltip', 'title' => \Yii::t('app', "The IP of your private instance will become visible once its powered up.")]);
else
  $display_ip = Html::tag('b', $target_ip, ["class" => 'text-primary', 'data-toggle' => 'tooltip', 'title' => \Yii::t('app', "The IP of your private instance.")]);

Card::begin([
  'header' => 'header-icon',
  'type' => 'card-stats bg-dark',
  'encode' => false,
  'icon' => sprintf('<img src="%s" class="img-fluid" style="max-width: 10rem; max-height: 4rem;" />', $target->logo),
  'color' => 'target',
  'subtitle' => "",
  'title' => Html::a(sprintf('%s / %s', $target->name, $display_ip), ['/target/default/view', 'id' => $target->id], ['class' => 'text-primary', 'style' => 'font-size: 0.9em;']),
  'footer' => false,
]);

Card::end();
