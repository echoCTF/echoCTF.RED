<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\frontend\models\PlayerSslSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title='Player VPN Certificates';
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="player-ssl-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Player VPN Certificate', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
              'attribute' => 'player',
              'label'=>'Player',
              'value'=> function($model) {return sprintf("id:%d %s", $model->player_id, $model->player->username);},
            ],
            [
              'attribute' => 'subject',
              'label'=>'Subject',
              'value'=> function($model) {return $model->subjectString;},
            ],
            //'privkey:ntext',
            'ts',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>


</div>
