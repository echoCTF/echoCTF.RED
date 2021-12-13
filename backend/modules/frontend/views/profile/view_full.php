<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
//use yii\bootstrap\Tabs;
use kartik\tabs\TabsX;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\Profile */

$this->title="View Profile for ".Html::encode($model->owner->username)." profile: ".$model->id;
$this->params['breadcrumbs'][]=['label' => Yii::t('app', 'Profiles'), 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
\yii\web\YiiAsset::register($this);
?>
<section class="container">
  <div class="profile-view-full">
    <!-- Begin .page-heading -->
    <p></p>
    <?=$this->render('_heading',['model'=>$model]);?>
    <div class="row">
      <div class="col-md-4">
        <?=$this->render('_player_details',['model'=>$model]);?>
        <?=$this->render('_player_counters_details',['model'=>$model]);?>
        <?=$this->render('_player_date_details',['model'=>$model]);?>
        <?=$this->render('_player_meta_details',['model'=>$model]);?>
        <?=$this->render('_player_relations',['model'=>$model]);?>
        <?=$this->render('_player_badges',['model'=>$model]);?>
      </div>
      <div class="col-md-8">
        <div class="tab-block">
<?php
echo TabsX::widget([
    'position' => TabsX::POS_ABOVE,
    'align' => TabsX::ALIGN_LEFT,
    'items' => [
        [
          'label' => 'Activity',
          'content'=>$this->render('_activity_tab',['model'=>$model]),
          'headerOptions' => ['style'=>'font-weight:bold'],
          'options' => ['id' => 'stream-tab'],
          'active'=>true,
        ],
        [
          'label' => 'VPN History',
          'headerOptions' => ['style'=>'font-weight:bold'],
          'linkOptions'=>['data-url'=>Url::to(['vpn-history', 'id' => $model->id])],
          'options' => ['id' => 'vpn-history-tab'],
        ],
    ],
]);
?>
        </div>
      </div>
    </div>
  </div>
</section>
