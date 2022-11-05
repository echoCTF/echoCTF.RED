<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use app\widgets\stream\StreamWidget as Stream;
use app\widgets\target\TargetWidget;

$this->title=Yii::$app->sys->event_name.' '.\Yii::t('app','Network details').' ['.Html::encode($model->name).']';
$this->_fluid="-fluid";
$module = \app\modules\network\Module::getInstance();

?>
<div class="network-view">
  <div class="body-content">
    <?php if($module->checkNetwork($model)===false):?>
    <div class="row d-flex justify-content-center">
      <div class="col-sm-12 col-md-6 col-xl-4 alert alert-danger d-flex justify-content-center" role="alert">
        <b><?=\Yii::t('app',"You don't have access to this network.")?></b>
      </div>
    </div>
    <?php endif;?>
    <h3><?=\Yii::t('app','Details for Network [<code>{network_name}</code>]',['network_name'=>Html::encode($model->name)])?></h3>
    <hr />
    <div class="row">
      <div class="col-md-8">
        <?php \yii\widgets\Pjax::begin(['id'=>'target-listing', 'enablePushState'=>false, 'linkSelector'=>'#target-pager a', 'formSelector'=>false]);?>
        <?php echo TargetWidget::widget(['dataProvider' => $networkTargetProvider, 'player_id'=>Yii::$app->user->id, 'profile'=>Yii::$app->user->identity->profile, 'title'=>\Yii::t('app','Progress'), 'category'=>\Yii::t('app','Network targets'), 'personal'=>false,'hidden_attributes'=>['id']]);?>
        <?php \yii\widgets\Pjax::end()?>

      </div>
      <div class="col-md-4">
        <div class="card card-profile">
          <div class="card-icon bg-primary">
            <img class="img" src="<?=$model->icon?>" height="80vw"/>
          </div>
          <div class="card-body table-responsive">
            <h4 class="card-title"><?=Html::encode($model->name)?></h4>
            <h6 class="badge badge-primary"><?=\Yii::t('app','{targetsCount,plural,=0{no targets} =1{# target} other{# targets}}',['targetsCount'=>$model->targetsCount])?></h6>
            <p style="text-align: justify;"><?=$model->description?></p>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
