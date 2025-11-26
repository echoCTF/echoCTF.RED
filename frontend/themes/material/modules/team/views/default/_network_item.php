<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

$item_classes = [
  'list-group-item',
  'list-group-item-action',
  'd-flex',
//  'justify-content-between',
  'align-items-center',
  'text-primary',
  'orbitron',
  'rounded'
];

$text=Html::img('/images/avatars/' . $model->player->profile->avtr, ['class' => 'img rounded', 'style' => 'max-width: 30px; max-height: 30px; margin-right: 10px;'])." ".\Yii::t('app', "{username}'s network ({n,plural,=0{no targets} =1{1 target} other{# targets}})", ['n' =>  count($model->privateTargets),'username'=>$model->player->username]);
?>
<?= Html::a($text, ['/network/private/view', 'id' => $model['id']], ['class' => implode(' ', $item_classes),'style'=>'font-size: 1.1em !important;']); ?>