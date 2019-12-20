<?php
    $menu = $img = "";
    $config = new app\assets\MaterialAssetConfig();
    $menu = app\models\Menu::getMenu();
    $img = $config::sidebarBackgroundImage();
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="sidebar" data-color="<?=$config::sidebarColor()  ?>" data-background-color="<?=$config::sidebarBackgroundColor()  ?>">
    <div class="logo">
        <a href="/" class="simple-text logo-mini">
            <img src="<?=$config::logoMini();?>" class="img-fluid">
        </a>
        <?php if(!Yii::$app->user->isGuest):?>
          <a href="<?=Url::to(['/profile/me'])?>" class="simple-text logo-normal" style="text-transform:none">
            <img src="/images/avatars/<?=Yii::$app->user->identity->profile->avatar;?>" class="img-fluid rounded-corners" style="max-width: 40px; max-height: 50px"><br/>
            <?=Html::encode(Yii::$app->user->identity->username)?> <small style="font-size: 0.65em">(<code><?=number_format(Yii::$app->user->identity->profile->score->points)?> pts</code>)</small>
          </a>
        <?php endif;?>
    </div>
    <div class="sidebar-wrapper">
        <?=$menu?>
    </div>
</div>
