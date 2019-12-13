<?php
use yii\helpers\Html;
use rce\material\widgets\Noti;
use app\assets\MaterialAsset;
$bundle=MaterialAsset::register($this);
//$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/ricar2ce/yii2-material-theme/assets');
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
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">
    <?php $this->head() ?>

    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
</head>
	<body>
		<?php $this->beginBody() ?>
		  <div class="wrapper">
		    <?=$this->render('left.php')?>
		    <div class="main-panel">
		    	<?=$this->render('header.php');?>
			    <div class="content">
			    	<div class="container<?=$this->_fluid?>">
                  <?= Noti::widget() ?>
            			<?= $content ?>
			    	</div>
			    </div>
		    </div>
		  </div>
		<?php $this->endBody() ?>
	</body>
</html>
<?php $this->endPage() ?>
