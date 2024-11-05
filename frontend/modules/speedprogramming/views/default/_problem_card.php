<?php

use app\widgets\Card;
use yii\helpers\Html;
use app\modules\game\models\Headshot;

switch ($model->difficulty) {
  case 1:
    $color = 'primary';
    break;
  case 2:
    $color = 'warning';
    break;
  case 3:
    $color = 'danger';
    break;
  default:
    $color = 'primary';
}
$suffix = '';

?>
<div class="col-xl-4 col-lg-6 col-md-5 col-sm-12 d-flex align-items-stretch">
  <?php Card::begin([
    'header' => 'header-icon',
    'type' => 'card-stats',
    'encode' => false,
    'icon' => sprintf('<img src="/images/problem/_%s-thumbnail.png" class="img-fluid" style="max-width: 10rem; max-height: 4rem;"/>%s', $model->name, $suffix),
    'color' => $color,
    //'subtitle'=>Html::a(sprintf('%s', long2ip($model->ip)),['/speedprogramming/default/view','id'=>$model->id],['class'=>'text-primary font-weight-bold']),
    'title' => Html::a(sprintf('%s (%s)', $model->name, $model->difficultyText), ['/speedprogramming/default/view', 'id' => $model->id], ['class' => 'text-primary font-weight-bold']),
    'footer' => Html::a("Details$suffix", ['/speedprogramming/default/view', 'id' => $model->id], ['class' => 'btn btn-' . $color]),
  ]);
  Card::end(); ?>
</div>