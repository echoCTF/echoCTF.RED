<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\widgets\Twitter;
use app\widgets\Card;
$subscribe=null;
if(array_key_exists('subscription',Yii::$app->modules)!==false)
{
  $module = \app\modules\network\Module::getInstance();
  if($module->checkNetwork($model)===false)
  {
    $subscribe="<p>".Html::a('Subscribe',
              Url::to(['/subscription/default/index']),
              [
                'title' => 'Subscribe to access this network',
                'class'=>'btn btn-danger text-dark font-weight-bold',
                'aria-label'=>'Subscribe to access this network',
                'data-pjax' => '0',
              ]
          )."</p>";
  }
  else {
    $subscribe="<p>".Html::a('Go to Network',
              Url::to(['/network/default/view','id'=>$model->id]),
              [
                'title' => 'Go to network',
                'class'=>'btn btn-primary text-dark font-weight-bold',
                'aria-label'=>'Go to network',
                'data-pjax' => '0',
              ]
          )."</p>";
  }
}
?>
<div class="col col-lg-4 col-md-6 col-sm-6 d-flex align-items-stretch">
  <div class="card card-profile">
    <div class="card-icon bg-primary">
      <img class="img" src="<?=$model->icon?>" height="80vw"/>
    </div>
    <div class="card-body table-responsive">
      <h4 class="font-weight-bold"><?=Html::a($model->name ,
                  Url::to(['/network/default/view', 'id'=>$model->id]),
                  [
                    'style'=>'float: bottom;',
                    'title' => 'View Network details',
                    'aria-label'=>'View network details',
                    'data-pjax' => '0',
                  ]
              );?></h4>
              <h6 class="badge badge-primary"><?=$model->targetsCount?> targets</h6>
      <p style="text-align: justify;"><?=$model->description?></p>
  </div>
  <div class="card-footer">
    <p class="small">This network is <?=($model->public ? 'public' : 'private')?></p>
    <?=$subscribe?>
  </div>
 </div>
</div>
