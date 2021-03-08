<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use app\widgets\stream\StreamWidget as Stream;
use app\widgets\target\TargetWidget;

$this->title=Yii::$app->sys->event_name.' Network details ['.Html::encode($model->name).']';
$this->_fluid="-fluid";
$module = \app\modules\network\Module::getInstance();

?>
<div class="network-view">
  <div class="body-content">
    <?php if($module->checkNetwork($model)===false):?>
    <div class="row d-flex justify-content-center">
      <div class="col-sm-12 col-md-6 col-xl-4 alert alert-danger d-flex justify-content-center" role="alert">
        <b>You don't have access to this network.</b>
      </div>
    </div>
    <?php endif;?>
    <h3>Details for Network [<code><?=Html::encode($model->name)?></code>]</h3>
    <hr />
    <div class="row">
      <div class="col-md-8">
        <?php \yii\widgets\Pjax::begin(['id'=>'target-listing', 'enablePushState'=>false, 'linkSelector'=>'#target-pager a', 'formSelector'=>false]);?>
        <?php echo TargetWidget::widget(['dataProvider' => $networkTargetProvider, 'player_id'=>Yii::$app->user->id, 'profile'=>Yii::$app->user->identity->profile, 'title'=>'Progress', 'category'=>'Network targets', 'personal'=>false]);?>
        <?php \yii\widgets\Pjax::end()?>

      </div>
      <div class="col-md-4">
        <div class="card card-profile">
          <div class="card-icon bg-primary">
            <img class="img" src="<?=$model->icon?>" height="80vw"/>
          </div>
          <div class="card-body table-responsive">
            <h4 class="card-title"><?=Html::encode($model->name)?></h4>
            <h6 class="badge badge-primary"><?=$model->targetsCount?> targets</h6>
            <p style="text-align: justify;"><?=Html::encode($model->description)?></p>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
