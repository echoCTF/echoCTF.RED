<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\frontend\models\PlayerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="player-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Player', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Import Players', ['import'], ['class' => 'btn btn-info']) ?>
        <?= Html::a('Reset All player data', ['reset-playdata'], ['class' => 'btn btn-danger', 'data' => ['confirm' => 'Are you sure you want to delete all player data?', 'method' => 'post', ]]) ?>
        <?= Html::a('Reset All player progress', ['reset-player-progress'], ['class' => 'btn btn-warning', 'data' => ['confirm' => 'Are you sure you want to delete all player progress?', 'method' => 'post', ]]) ?>
    </p>
<?php
use miloschuman\highcharts\Highcharts;
echo Highcharts::widget([
    'options' => [
      'title' => ['text' => 'Registrations Per day'],
      'xAxis' => [
          'categories' => array_values($categories)
      ],
      'yAxis' => [
          'title' => ['text' => 'Users']
      ],
      'series' => [
          ['name' => 'Registrations', 'data' => $registrations],
      ]
    ]
]);

echo Highcharts::widget([
    'options' => [
      'title' => ['text' => 'Claims per day'],
      'xAxis' => [
          'categories' => array_values($claimDates)
      ],
      'yAxis' => [
          'title' => ['text' => 'Claims']
      ],
      'series' => [
          ['name' => 'Treasure claims', 'data' => $claims],
      ]
    ]
]);
?>
</div>
