<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\frontend\models\PlayerSslSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title='Player VPN Certificates';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="player-ssl-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Player VPN Certificate', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'serial',
            ['class' => 'app\components\columns\ProfileColumn','attribute'=>'player'],
            [
              'attribute' => 'subject',
              'label'=>'Subject',
              'value'=> function($model) {return $model->subjectString;},
            ],
            //'privkey:ntext',
            [
              'attribute'=>'ts',
              'format'=>'raw',
              'value'=>function($model){ return sprintf("%s / %s",$model->ts,Yii::$app->formatter->format($model->ts, 'relativeTime'));},
              'contentOptions' => ['class' => 'small'], // For TD

            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>


</div>
