<?php

use app\modules\sales\models\PlayerProduct;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\modules\sales\models\PlayerProductSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Player Purchases');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-product-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a(Yii::t('app', 'Create Player Product'), ['create'], ['class' => 'btn btn-success']) ?>
  </p>

  <?php Pjax::begin(); ?>
  <?php // echo $this->render('_search', ['model' => $searchModel]);
  ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      'id',
      ['class' => 'app\components\columns\ProfileColumn'],
      'player_id',
      [
        'attribute' => 'product_name',
        'value' => function ($model) {
          if ($model->product)
            return $model->product->name;
          if ($model->price_id === 'price_vip')
            return 'VIP';
          return '';
        }
      ],
      [
        'attribute' => 'price_id',
        'format' => 'raw',
        'value' => function ($model) {
          if($model->price_id==='price_vip')
            return 'VIP';

          if ($model->price)
          {
            return sprintf('<small><abbr title="%s">%d%s every %d %s</abbr></small>', $model->price_id, intval($model->price->unit_amount / 100), strtoupper($model->price->currency), $model->price->interval_count, $model->price->recurring_interval);
          }
          return $model->price_id;
        }
      ],
      'ending',
      'metadata',
      //'created_at',
      //'updated_at',
      [
        'class' => ActionColumn::className(),
        'urlCreator' => function ($action, PlayerProduct $model, $key, $index, $column) {
          return Url::toRoute([$action, 'id' => $model->id]);
        }
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>