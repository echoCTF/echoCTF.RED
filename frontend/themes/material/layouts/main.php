<?php
use yii\helpers\Html;
use app\widgets\Noti;
use app\assets\MaterialAsset;
Yii::$app->timeZone=Yii::$app->sys->time_zone ?: 'UTC';
date_default_timezone_set(Yii::$app->sys->time_zone ?: 'UTC');

$bundle=MaterialAsset::register($this);
//$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/ricar2ce/yii2-material-theme/assets');
$this->registerMetaTag(['name'=>'description', 'content'=>trim($this->_description)], 'description');
//$this->_url=\yii\helpers\Url::to([null],'https');

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
$this->registerJsOverrides();
$this->registerCssOverrides();
$this->registerLayoutOverrides();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language ?>">
<head>
    <meta charset="<?=Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=1, shrink-to-fit=1' name='viewport' />
    <link rel="preconnect" href="//fonts.googleapis.com" crossorigin="anonymous">
    <link rel="preload" as="style" href="//fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons|Roboto+Mono|Orbitron&display=swap" crossorigin="anonymous">
    <link rel="preload" as="font" href="/webfonts/fa-solid-900.woff2" crossorigin>
    <link rel="preload" as="font" href="/webfonts/fa-brands-400.woff2" crossorigin>
    <link rel="stylesheet" media="print" onload="this.onload=null;this.removeAttribute('media');" href="//fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons|Roboto+Mono|Orbitron&display=swap" crossorigin="anonymous">
    <noscript>
      <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons|Roboto+Mono|Orbitron&display=swap" crossorigin="anonymous">
    </noscript>
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
        <div class="container-fluid">
            <?=\Yii::$app->sys->{"footer_logos"}?>
          <div class="copyright float-right">
            &copy; <?=date("Y")?>, made with <i class="material-icons text-danger">favorite</i> by
            <a href="https://www.echothrust.com" target="_blank">echothrust</a> with <a href="https://echoctf.com/" target="_blank"><b class="text-white">echo</b><b>CTF</b></a>
          </div>
        </div>
      </footer>
    </div>
  </div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
