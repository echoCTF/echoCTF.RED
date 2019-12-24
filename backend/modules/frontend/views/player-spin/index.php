<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\frontend\models\PlayerSpinSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Player Spins');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="player-spin-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Spin'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Reset all spins'), ['reset'], ['class' => 'btn btn-info',
          'data' => [
              'confirm' => Yii::t('app', 'Are you sure you want to reset all counters?'),
              'method' => 'post',
          ],
        ]) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
              'attribute' => 'player',
              'label'=>'Player',
              'value'=> function($model) {return sprintf("id:%d %s",$model->player_id,$model->player->username);},
            ],
            'counter',
            'total',
            'updated_at:date',
            'ts',

            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{reset} {view} {update} {delete}',
              'buttons' => [
                  'reset' => function ($url) {
                      return Html::a(
                          '<span class="glyphicon glyphicon-refresh"></span>',
                          $url,
                          [
                              'title' => 'Reset Counters',
                              'data-pjax' => '0',
                              'data-method' => 'POST',
                          ]
                      );
                  },
              ],
            ],
        ],
    ]); ?>


</div>
