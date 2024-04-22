<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\PlayerFindingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
yii\bootstrap5\Modal::begin([
    'title' => '<h2><i class="bi bi-info-circle-fill"></i> '.Html::encode($this->title).' Help</h2>',
    'toggleButton' => ['label' => '<i class="bi bi-info-circle-fill"></i> Help', 'class' => 'btn btn-info'],
  'options'=>['class'=>'modal-lg']
]);
echo yii\helpers\Markdown::process($this->render('help/'.$this->context->action->id), 'gfm');
yii\bootstrap5\Modal::end();
?>
<div class="player-finding-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Player Finding', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
          'player_id',
          ['class' => 'app\components\columns\ProfileColumn','attribute'=>'player'],
          [
            'attribute' => 'target_id',
            'label'=>'Target ID',
            'value'=> 'finding.target_id',
          ],
          'finding_id',
          [
            'attribute' => 'finding',
            'label'=>'Finding',
            'format'=>'html',
            'value'=> 'finding.name',
          ],
          [
            'attribute' => 'points',
            'label'=>'Points',
            'value'=> 'points',
          ],
          'ts',

//            'player_id',
//            [
//                'attribute' => 'player',
//                'label'=>'Player',
//                'value'=> function($model) {return sprintf("id:%d %s", $model->player_id, $model->player->username);},
//            ],
//            'finding_id',
//            [
//              'attribute' => 'finding',
//              'label'=>'Finding',
//              'value'=> function($model) {return sprintf("id:%d %s", $model->finding_id, $model->finding->name);},
//            ],
//            'ts',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>


</div>
