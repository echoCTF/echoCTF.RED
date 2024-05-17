<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\content\models\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'News');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
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
              'template' => '{view} {update} {delete} {discord}',
              'visibleButtons'=>[
                'discord' => \Yii::$app->sys->discord_news_webhook!==false && trim(\Yii::$app->sys->discord_news_webhook)!=="",
              ],
              'buttons' => [
                'discord' => function($url, $model) {
                  return Html::a('<img src="/images/discord_clyde_purple.svg" width="18px">', ['discord', 'id' => $model->id], [
                      'class' => '',
                      'title'=>'Post news entry to a discord webhook',
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
