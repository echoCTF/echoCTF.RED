<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\content\models\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'News');
$this->params['breadcrumbs'][] = $this->title;
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap\Modal::end();
?>
<div class="news-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create News'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title',
            'category',
            'created_at',
            'updated_at',

            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{view} {update} {delete} {discord}', // <-- your custom action's name
              'buttons' => [
                'discord' => function($url, $model) {
                  return Html::a('<img src="/images/discord_clyde_purple.svg" width="18px">', ['discord', 'id' => $model->id], [
                      'class' => '',
                      'data' => [
                          'confirm' => 'Are you absolutely sure you want to send this news to the webhook?',
                          'method' => 'post',
                        ],
                  ]);
                },
              ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
