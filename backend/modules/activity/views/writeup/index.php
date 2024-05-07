<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\WriteupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Writeups';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
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
            'id',
            ['class' => 'app\components\columns\ProfileColumn'],
            //'target_id',
            [
              'attribute'=>'fqdn',
              'format'=>'html',
              'contentOptions'=>['class'=>'text-nowrap','style'=>'min-width: 16em; max-width: 16em'],
              'value'=>function($model){return sprintf("%s (<small>%s</small>)",$model->target->name,$model->target->ipoctet);},
            ],
            [
              'attribute'=>'content',
              'contentOptions'=>['class'=>'wordwrap', 'style'=>'min-width: 30em; max-width: 30em'],
              'value'=>function($model){return wordwrap(substr($model->content,0,256),80);}
            ],
            'approved:boolean',
            [
              'attribute'=>'status',
              'filter'=>['OK'=>'OK','PENDING'=>'PENDING','REJECTED'=>'REJECTED','NEEDS FIXES'=>'NEEDS FIXES'],
            ],
            [
              'attribute'=>'lang',
              'label'=>'Language',
              'value'=>'language.l'

            ],
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
                          '<i class="bi bi-check-circle-fill"></i>',
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
