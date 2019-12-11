<?php
use yii\helpers\Html;
use yii\widgets\ListView;
$this->title = Yii::$app->sys->event_name .' - Target: '.$target->name.' #'.$target->id;
#$this->pageDescription=CHtml::encode($target->purpose);
#$this->pageImage=Yii::app()->getBaseUrl(true)."/images/targets/".$target->name.".png";
#$this->pageURL=$this->createAbsoluteUrl('target/view',array('id'=>$target->id));
#Yii::$app->clientScript->registerCssFile(Yii::app()->baseUrl . '/css/scores.css');
$this->registerCssFile("@web/css/scores.css", [
  //  'depends' => [\yii\bootstrap\BootstrapAsset::className()],
    'media' => 'screen',
], 'scores-theme');
?>

<div class="target-index">
  <div class="body-content">
    <div class="watermarked img-fluid">
    <?=sprintf('<img src="/images/targets/_%s.png" width="100px"/>',$target->name)?>
    </div>

    <?php
    if(Yii::$app->user->isGuest)
      echo $this->render('_guest',['target'=>$target,'playerPoints'=>$playerPoints]);
    else
      echo $this->render('_versus',['target'=>$target,'playerPoints'=>$playerPoints]);
     ?>

     <div class="card">
       <div class="card-header card-header-primary">
         <h4 class="card-title">Activity on target</h4>
       </div>
       <div class="card-body table-responsive">
        <?php \yii\widgets\Pjax::begin(); ?>
        <?php echo ListView::widget([
              'id'=>'target-activity',
              'dataProvider' => $streamProvider,
              'summary'=>false,
              'itemOptions' => [
                'tag' => false
              ],
              'itemView' => '_stream',
              'pager'=>[
                'firstPageLabel' => '<i class="fas fa-step-backward"></i>',
                'lastPageLabel' => '<i class="fas fa-step-forward"></i>',
                'maxButtonCount'=>3,
                'disableCurrentPageButton'=>true,
                'prevPageLabel'=>'<i class="fas fa-chevron-left"></i>',
                'nextPageLabel'=>'<i class="fas fa-chevron-right"></i>',
                'class'=>'yii\bootstrap4\LinkPager',
              ],
          ]);?>
        <?php \yii\widgets\Pjax::end(); ?>
      </div>
    </div>
  </div>
</div>
