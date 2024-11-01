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

if($progress===false)
  $text =$model['name'];
else
{
  $bar=\yii\bootstrap4\Progress::widget(['percent' => intval(floor($model->progress)), 'options' => ['style' => 'min-width: 7vw']]);
  $text=Html::img("/images/targets/_" . $model['name'] . '-thumbnail.png', ['width'=>'28px','style' => 'max-height: 28px; max-width: 28px']).$model['name'].$bar;
}
?>
<?= Html::a($text, ['/challenge/default/view', 'id' => $model['id']], ['class' => implode(' ', $item_classes),'style'=>'font-size: 1.1em !important;']); ?>
