<?php
use yii\helpers\Html;
use app\assets\MaterialAsset;
Yii::$app->timeZone=Yii::$app->sys->time_zone ?: 'UTC';
date_default_timezone_set(Yii::$app->sys->time_zone ?: 'UTC');
$bundle=MaterialAsset::register($this);

//$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/ricar2ce/yii2-material-theme/assets');
$this->registerMetaTag(['name'=>'article:author', 'content'=>'echoCTF']);
$this->registerMetaTag(['name'=>'description', 'content'=>trim($this->_description)], 'description');
//$this->_url=\yii\helpers\Url::to([null],'https');

$this->registerMetaTag($this->og_title, 'og_title');
$this->registerMetaTag($this->og_site_name, 'og_site_name');
$this->registerMetaTag($this->og_description, 'og_description');
$this->registerMetaTag($this->og_url, 'og_url');
$this->registerMetaTag($this->og_image, 'og_image');
$this->registerMetaTag(['name'=>'og:image:width', 'content'=>'1200']);
$this->registerMetaTag(['name'=>'og:image:height','content'=>'628']);

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
    <style>
      body { text-align: center; padding: 150px; background: #222; font: 20px Helvetica, sans-serif; color: #cecece;}
      h1 { font-size: 50px; }
      article { display: block; text-align: left; width: 650px; margin: 0 auto; }
      a { color: #dc8100; text-decoration: none; }
      a:hover { color: #333; text-decoration: none; }
    </style>
    <title><?=trim(Html::encode($this->title))?></title>
</head>
<body>

<?php $this->beginBody() ?>
      <?=$content?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
