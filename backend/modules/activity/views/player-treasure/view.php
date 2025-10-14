<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerTreasure */

$this->title=$model->player_id;
$this->params['breadcrumbs'][]=ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][]=['label' => 'Player Treasures', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="player-treasure-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'player_id' => $model->player_id, 'treasure_id' => $model->treasure_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'player_id' => $model->player_id, 'treasure_id' => $model->treasure_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
          ['class' => 'app\components\columns\ProfileColumn','attribute'=>'player.username'],
          [
            'attribute' => 'treasure',
            'label'=>'Treasure',
            'value'=> function($model) {return sprintf("id:%d %s", $model->treasure_id, $model->treasure->name);},
          ],
          'points',
          'ts',
        ],
    ]) ?>

</div>
