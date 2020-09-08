<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\WriteupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Writeups';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="writeup-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Writeup', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'player_id',
            [
              'attribute'=>'username',
              'value'=>'player.username',
            ],
            'target_id',
            [
              'attribute'=>'fqdn',
              'value'=>'target.fqdn',
            ],
            [
              'attribute'=>'ipoctet',
              'value'=>'target.ipoctet',
            ],
            'content',
            'approved:boolean',
            [
              'attribute'=>'status',
              'filter'=>['OK'=>'OK','PENDING'=>'PENDING','REJECTED'=>'REJECTED','NEEDS FIXES'=>'NEEDS FIXES'],
            ],
            'comment',
            'created_at',
            'updated_at',

            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{approve} {view} {update} {delete}',
              'visibleButtons' => [
                  'approve' => function ($model) {
                      return !$model->approved || $model->status!=='OK';
                  },
              ],
              'buttons' => [
                  'approve' => function ($url) {
                      return Html::a(
                          '<span class="glyphicon glyphicon-ok"></span>',
                          $url,
                          [
                              'title' => 'Approve writeup',
                              'data-method'=>'post',
                              'data-pjax' => '0',
                          ]
                      );
                  },
              ],
            ],
        ],
    ]); ?>


</div>
