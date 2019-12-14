<?php
    $menu = $img = "";
    $config = new app\assets\MaterialAssetConfig();
    $menu = app\models\Menu::getMenu();
    $img = $config::sidebarBackgroundImage();
use yii\helpers\Html;
?>
<div class="sidebar" data-color="<?=$config::sidebarColor()  ?>" data-background-color="<?=$config::sidebarBackgroundColor()  ?>">
    <div class="logo">
        <a href="#" class="simple-text logo-mini">
            <img src="<?=$config::logoMini();?>" style="img-fluid">
        </a>
        <?php if(Yii::$app->user->isGuest):?>
          <a href="#" class="simple-text logo-normal"><?=$config::siteTitle()?></a>
        <?php else:?>
          <a href="#" class="simple-text logo-normal" style="text-transform:none"><?=Html::encode(Yii::$app->user->identity->username)?> <small>(<?=number_format(Yii::$app->user->identity->profile->score->points)?> pts)</small></a>
        <?php endif;?>
    </div>
    <div class="sidebar-wrapper">
        <?=$menu?>
    </div>
</div>
