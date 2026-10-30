<?php \app\widgets\Card::begin([
  'header' => 'header-icon',
  'type' => 'card-stats reverse',
  'icon' => \yii\helpers\Html::img('/images/extras.png', ['style' => 'width: 50px;']),
  'encode' => false,
  'color' => 'rose',
  'title' => '<b>' . $model->product->htmlOptions('title') . '</b>',
  'subtitle' => '<small>' . (strtotime($model->ending) > time()
    ? \Yii::t('app', 'Expires {time}', ['time' => \Yii::$app->formatter->asRelativeTime($model->ending)])
    : \Yii::t('app', 'Expired {time}', ['time' => \Yii::$app->formatter->asRelativeTime($model->ending)])) . '</small>',
  // Only perks with a configure page (views/perk/<shortcode>.php, eg Team Network) get the button
  'footer' => preg_match('/^[A-Za-z0-9_]+$/', (string) $model->product->shortcode) && file_exists(__DIR__ . '/../perk/' . $model->product->shortcode . '.php')
    ? \yii\helpers\Html::a(\Yii::t('app', 'Configure'), \yii\helpers\Url::toRoute(['/subscription/perk/configure', 'id' => $model->id]), [
      'class' => 'h4 font-weight-bold btn btn-outline-rose btn-block'
    ])
    : false,
]); ?>
<?= strip_tags($model->product->perks) ?>
<?php \app\widgets\Card::end(); ?>