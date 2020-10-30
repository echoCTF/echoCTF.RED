<?php
use yii\helpers\Html;
use app\widgets\Noti;
use app\assets\MaterialAsset;
$bundle=MaterialAsset::register($this);
//$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/ricar2ce/yii2-material-theme/assets');
$this->registerMetaTag(['name'=>'description', 'content'=>$this->_description], 'description');
$this->registerMetaTag($this->og_title, 'og_title');
$this->registerMetaTag($this->og_site_name, 'og_site_name');
$this->registerMetaTag($this->og_description, 'og_description');
$this->registerMetaTag($this->og_url, 'og_url');
$this->registerMetaTag($this->og_image, 'og_image');

$this->registerMetaTag($this->twitter_card, 'twitter_card');
$this->registerMetaTag($this->twitter_site, 'twitter_site');
$this->registerMetaTag($this->twitter_title, 'twitter_title');
$this->registerMetaTag($this->twitter_description, 'twitter_description');
$this->registerMetaTag($this->twitter_image, 'twitter_image');
$this->registerMetaTag($this->twitter_image_width, 'twitter_image_width');
$this->registerMetaTag($this->twitter_image_height, 'twitter_image_height');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language ?>">
<head>
    <meta charset="<?=Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <link rel="apple-touch-icon" href="/images/apple-touch-icon.png"/>
    <link rel="canonical" href="<?=$this->og_url['content']?>" />
    <?php $this->head()?>

    <?=Html::csrfMetaTags() ?>
    <title><?=trim(Html::encode($this->title))?></title>
</head>
<body>
<?php $this->beginBody() ?>
  <div class="wrapper">
    <?=$this->render('left.php')?>
    <div class="main-panel">
    	<?=$this->render('header.php');?>
	    <div class="content">
	    	<div class="container<?=$this->_fluid?>">
            <?=Noti::widget() ?>
      			<?=$content ?>
	    	</div>
	    </div>
      <footer class="footer">
            <?=Yii::$app->sys->footer_logos?>
            &copy; <?=date("Y")?> by&nbsp;<a href="https://www.echothrust.com/" target="_blank">echothrust</a>, made with <i class="material-icons text-danger">favorite</i> using&nbsp;
            <a href="https://github.com/echoCTF/echoCTF.RED" target="_blank"><b style="color: white">echo</b><b>CTF</b></a>
      </footer>
    </div>
  </div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
