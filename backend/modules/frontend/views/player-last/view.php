<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\activity\models\PlayerLast */

$this->title=$model->id;
$this->params['breadcrumbs'][]=['label' => Yii::t('app', 'Player Lasts'), 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="player-last-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'on_pui',
            'on_vpn',
            [
              'attribute'=>'vpn_remote_address',
              'label'=> 'VPN Remote IP',
              'value'=>function($model) { return long2ip($model->vpn_remote_address) ;}
            ],
            [
              'attribute'=>'vpn_local_address',
              'label'=> 'VPN Local IP',
              'value'=>function($model) { return long2ip($model->vpn_local_address) ;}
            ],

            [
              'attribute'=>'signup_ip',
              'value'=>function($model) {return $model->signup_ip === NULL ? null : long2ip($model->signup_ip);},
            ],
            [
              'attribute'=>'signin_ip',
              'value'=>function($model) {return $model->signin_ip === NULL ? null : long2ip($model->signin_ip);},
            ],
            'ts',
        ],
    ]) ?>

</div>
