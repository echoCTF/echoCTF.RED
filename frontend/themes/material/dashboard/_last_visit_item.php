<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

$item_classes = [
  'list-group-item',
  'list-group-item-action',
  'd-flex',
  'justify-content-between',
  'align-items-center',
  'text-primary',
  'orbitron',
  'rounded'
];

$text =$model['name']. ' ' . Html::img("/images/targets/_" . $model['name'] . '-thumbnail.png', ['style' => 'max-height: 28px;'])  ;
?>
<?= Html::a($text, ['/target/default/view', 'id' => $model['id']], ['class' => implode(' ', $item_classes),'style'=>'font-size: 1.1em !important;']); ?>
