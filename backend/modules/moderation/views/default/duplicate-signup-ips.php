<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\widgets\ProfileLink;
use app\widgets\ProfileActions;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\StreamSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = ucfirst(Yii::$app->controller->module->id) . ' / ' . ucfirst(Yii::$app->controller->id) . ' / Duplicate Signup IPs';
$this->params['breadcrumbs'][] = ['label' => ucfirst(Yii::$app->controller->module->id)];
$this->params['breadcrumbs'][] = ['label' => ucfirst(Yii::$app->controller->id), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => "Duplicate Signup IPs", 'url' => ['duplicate-signup-ips']];
?>
<?php
$rdnsUrl = \yii\helpers\Json::htmlEncode(\yii\helpers\Url::to(['reverse-dns']));
$this->registerJs(<<<JS
$(document).on('mouseenter', 'abbr.rdns:not([data-resolved])', function () {
  var el = $(this).attr('data-resolved', '1').attr('title', 'Resolving...');
  $.getJSON($rdnsUrl, {ip: el.data('ip')})
    .done(function (r) { el.attr('title', r.host); })
    .fail(function () { el.attr('title', 'Lookup failed').removeAttr('data-resolved'); });
});
JS);
?>
<div class="stream-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <?php // echo $this->render('_search', ['model' => $searchModel]);
  ?>
  <?php \yii\widgets\Pjax::begin(); ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      [
        'attribute' => 'signup_ip',
        'format'=>'raw',
        'value' => function ($model) {
          if ($model->signup_ip === NULL)
            return null;
          $ip = long2ip($model->signup_ip);
          // The hostname is resolved on hover (see reverse-dns action) so a
          // slow or failing DNS lookup never blocks rendering the page.
          return Html::tag('abbr', $ip, ['class' => 'rdns', 'data-ip' => $ip, 'title' => 'Hover to resolve']);
        },
        'headerOptions' => ['style' => 'width: 160px'],
      ],
      [
        'attribute' => 'duplicates',
        'contentOptions' => ['style' => 'width: 50px'],
      ],
      [
        'attribute' => 'offenders',
        'contentOptions' => ['style' => 'word-wrap:break-word'],
        'format' => 'html',
        'value' => function ($model) {
          $links = [];
          foreach (explode("\n", (string)$model->offenders) as $entry) {
            $parts = explode(':', $entry, 3);
            if (count($parts) !== 3)
              continue; // truncated entry
            [$player_id, $profile_id, $username] = $parts;
            if ($profile_id === '') {
              $links[] = Html::encode($username);
              continue;
            }
            $links[] = ProfileLink::widget([
              'username' => $username,
              'player_id' => $player_id,
              'profile_id' => $profile_id,
              'actions' => true
            ]);
          }
          return implode(", ", $links);
        }
      ],
    ],
  ]); ?>
  <?php \yii\widgets\Pjax::end(); ?>


</div>