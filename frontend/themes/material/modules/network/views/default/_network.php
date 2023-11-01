<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\widgets\Twitter;
use app\widgets\Card;
$subscribe=null;
$module = \app\modules\network\Module::getInstance();
if($module->checkNetwork($model)!==false)
{
  $subscribe="<p>".Html::a(\Yii::t('app','Go to Network'),
      Url::to(['/network/default/view','id'=>$model->id]),
      [
        'title' => \Yii::t('app','Go to network'),
        'class'=>'btn btn-primary text-dark font-weight-bold orbitron',
        'aria-label'=>\Yii::t('app','Go to network'),
        'data-pjax' => '0',
      ]
  )."</p>";
}
if(array_key_exists('subscription',Yii::$app->modules)!==false && $model->inProducts>0)
{
  if($module->checkNetwork($model)===false)
  {
    $subscribe="<p>".Html::a(\Yii::t('app','Subscribe'),
              Url::to(['/subscription/default/index']),
              [
                'title' => \Yii::t('app','Subscribe to access this network'),
                'class'=>'btn btn-danger text-dark font-weight-bold orbitron',
                'aria-label'=>\Yii::t('app','Subscribe to access this network'),
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
      <h4 class="orbitron"><?=Html::a($model->name ,
                  Url::to(['/network/default/view', 'id'=>$model->id]),
                  [
                    'style'=>'float: bottom;',
                    'title' => \Yii::t('app','View Network details'),
                    'aria-label'=>\Yii::t('app','View network details'),
                    'data-pjax' => '0',
                  ]
              );?></h4>
              <h6 class="badge badge-primary orbitron"><?=\Yii::t('app','{targetsCount,plural,=0{no targets} =1{# target} other{# targets}}',['targetsCount'=>$model->targetsCount])?></h6>
      <p style="text-align: justify;" ><?=$model->description?></p>
  </div>
  <div class="card-footer">
    <p class="small"><?=\Yii::t('app','This network is {pub_or_priv}',['pub_or_priv'=>($model->public ? \Yii::t('app','public') : \Yii::t('app','private'))])?></p>
    <?=$subscribe?>
  </div>
 </div>
</div>
