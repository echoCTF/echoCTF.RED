<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\infrastructure\models\TargetInstanceAuditSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Target Instance Audits');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="target-instance-audit-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'op',
            'player_id',
            'target_id',
            'server_id',
            'ip',
            'reboot',
            'ts',
        ],
    ]); ?>


</div>
