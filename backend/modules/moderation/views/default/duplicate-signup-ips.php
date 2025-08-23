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
          return $model->signup_ip === NULL ? null : '<abbr title="'.gethostbyaddr(long2ip($model->signup_ip)).'">'.long2ip($model->signup_ip)."</abbr>";
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
          foreach (explode(", ", $model->offenders) as $p) {
            $links[] = ProfileLink::widget([
              'username' => trim($p),
              'actions'=>true
            ]);
          }
          return implode(", ", $links);
        }
      ],
    ],
  ]); ?>
  <?php \yii\widgets\Pjax::end(); ?>


</div>