<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\frontend\models\CrlSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Crls');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crl-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Crl'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'columns' => [

            'id',
            'player_id',
            [
              'attribute'=>'subject',
              'value'=>function($model){ return wordwrap($model->subject,80,"\n",true);}
            ],
            [
              'attribute'=>'txtcrt',
              'value'=>function($model){ return explode("\n",$model->txtcrt)[3];}
            ],
            //'csr:ntext',
            //'crt:ntext',
            //'txtcrt:ntext',
            //'privkey:ntext',
            'ts',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
