<?php \app\widgets\Card::begin([
  'header' => 'header-icon',
  'type' => 'card-stats reverse',
  'icon' => \yii\helpers\Html::img('/images/extras.png', ['style' => 'width: 50px;']),
  'encode' => false,
  'color' => 'rose',
  'title' => '<b>' . $model->product->htmlOptions('title') . '</b>',
  'subtitle'=>'<small>Expires in '. \Yii::$app->formatter->asRelativeTime($model->ending).'</small>',
  'footer' => \yii\helpers\Html::a(\Yii::t('app', 'Configure'), \yii\helpers\Url::toRoute(['/subscription/perk/configure', 'id' => $model->product_id]), [
    'class' => 'h4 font-weight-bold btn btn-outline-rose btn-block',
    'data' => [
      'method' => 'post',
    ],
  ]),
]); ?>
<?= strip_tags($model->product->perks) ?>
<?php \app\widgets\Card::end(); ?>