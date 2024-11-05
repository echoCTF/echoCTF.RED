<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SpeedSolution */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Speed Solutions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="speed-solution-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <!--<?= Html::a('Validate', ['validate', 'id' => $model->id], ['class' => 'btn btn-info']) ?>-->
        <?= Html::a('Download', '/solutions/player_' . $model->player_id . '-target_'. $model->problem_id. '.'.$model->language, ['class' => 'btn btn-warning']) ?>

        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->status==='pending'):?>
        <?= Html::a('Approve', ['approve', 'id' => $model->id], [
            'class' => 'btn btn-success',
            'data' => [
                'confirm' => 'Are you sure you want to approve this submission with default points?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Reject', ['reject', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to reject this submission?',
                'method' => 'post',
            ],
        ]) ?>
        <?php endif;?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
          'id',
          [
           'attribute'=>'player.username',
           'label'=>'Player',
           'value'=>function($model){ return sprintf("(id: %d) %s",$model->player_id,$model->player->username); }
          ],
          [
            'attribute'=>'target.name',
            'label'=>'Challenge',
            'value'=>function($model){ return sprintf("(id: %d / difficulty: %d) %s",$model->problem_id,$model->problem->difficulty,$model->problem->name); }
          ],
          [
             'attribute' => 'language',
          ],
          [
             'attribute' => 'status',
          ],
          //'sourcecode',
          'points',
          [
            'attribute'=>'sourcecode',
            'format'=>'raw',
            'value'=>function($model){return '<pre>'.Html::encode(wordwrap($model->sourcecode,90)).'</pre>';}
          ],
          'created_at',
          'updated_at',
        ],
    ]) ?>

</div>
