<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerHint */

$this->title=$model->player_id;
$this->params['breadcrumbs'][]=ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][]=['label' => 'Player Hints', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="player-hint-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'player_id' => $model->player_id, 'hint_id' => $model->hint_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'player_id' => $model->player_id, 'hint_id' => $model->hint_id], [
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
            [
              'attribute' => 'player',
              'label'=>'Player',
              'value'=> function($model) {return sprintf("id:%d %s", $model->player_id, $model->player->username);},
            ],
            [
              'attribute' => 'hint',
              'label'=>'Hint',
              'value'=> function($model) {return sprintf("id:%d %s", $model->hint_id, $model->hint->title);},
            ],
            'status',
            'ts',
        ],
    ]) ?>

</div>
