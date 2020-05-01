<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\TeamScore */

$this->title=$model->team_id;
$this->params['breadcrumbs'][]=['label' => 'Team Scores', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="team-score-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->team_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->team_id], [
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
            'team_id',
            'points',
        ],
    ]) ?>

</div>
