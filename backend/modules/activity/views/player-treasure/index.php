<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\activity\models\PlayerTreasureSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="player-treasure-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Player Treasure', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'player_id',
            [
              'attribute' => 'player',
              'label'=>'Player',
              'value'=> 'player.username',
            ],
            [
              'attribute' => 'target_id',
              'label'=>'Target ID',
              'value'=> 'treasure.target_id',
            ],
            'treasure_id',
            [
              'attribute' => 'treasure',
              'label'=>'Treasure',
              'value'=> 'treasure.name',
            ],
            [
              'attribute' => 'points',
              'label'=>'Points',
              'value'=> 'points',
            ],
            'ts',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>


</div>
