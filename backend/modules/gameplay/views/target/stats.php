<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\gameplay\models\TargetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id) . ' Solving Statistics';
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="target-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'name',
            'difficulty',
            'startedBy',
            'solvedBy',
            'solvedPct',
            'fastestSolve:duration',
            'avgSolve:duration'
        ],
    ]);?>


</div>
