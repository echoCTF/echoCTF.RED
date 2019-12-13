<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\components\JustGage;
use app\widgets\stream\StreamWidget as Stream;
use app\widgets\target\TargetWidget;

$this->title = Yii::$app->sys->event_name .' - Profile of '.Html::encode($profile->owner->username);
//$this->pageDescription=Html::encode(str_replace("\n","",strip_tags($profile->bio)));
//$this->pageImage=Yii::$app->getBaseUrl(true)."/images/avatars/".$profile->avatar;
//$this->pageURL=$this->createAbsoluteUrl('/profile/index',array('id'=>$profile->id));
//$this->registerCssFile("@web/css/scores.css", [
//    'media' => 'screen',
//], 'scores-theme');

$this->_fluid="-flud";

?>
<!-- <center><img src="/images/logo.png" width="60%"/></center>
<hr>-->
<div class="profile-index">
  <div class="body-content">
    <div class="row">
      <div class="col-md-8">
        <?php echo TargetWidget::widget(['dataProvider' => $targetProgressProvider,'title'=>'Progress','category'=>'Progress of '.Html::encode($profile->owner->username).' on platform targets','personal'=>true]);?>

      </div>
      <div class="col-md-4">
        <?=$this->render('_card',['profile'=>$profile,'playerSpin'=>$playerSpin]);?>
      </div><!-- // end profile card col-md-4 -->
    </div><!--/row-->
    <?php
    echo Stream::widget(['divID'=>'stream','dataProvider' => $streamProvider,'pagerID'=>'stream-pager']);
    ?>
  </div><!--//body-content-->
</div><!--//profile-index-->
