<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\frontend\models\TeamSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][]=$this->title;
yii\bootstrap\Modal::begin([
    'header' => '<h2><span class="glyphicon glyphicon-question-sign"></span> '.$this->title.' Help</h2>',
    'toggleButton' => ['label' => '<span class="glyphicon glyphicon-question-sign"></span> Help','class'=>'btn btn-info'],
]);
echo $this->render('help/'.$this->context->action->id);
yii\bootstrap\Modal::end();
?>
<div class="team-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Team', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'name',
            'description:ntext',
            'academic:boolean',
            'logo',
            'owner.username',
            //'token',
            'ts',

            [
              'class' => 'yii\grid\ActionColumn',
              'template' => '{toggle-academic} {view} {update} {delete}',
              'buttons' => [
                  'toggle-academic' => function($url) {
                      return Html::a(
                          '<span class="glyphicon glyphicon glyphicon-education"></span>',
                          $url,
                          [
                              'title' => 'Toggle team academic flag',
                              'data-pjax' => '0',
                              'data-method' => 'POST',
                          ]
                      );
                  },
              ],
            ],
        ],
    ]);?>


</div>
