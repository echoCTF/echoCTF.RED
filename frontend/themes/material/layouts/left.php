<?php
    $menu=$img="";
    $config=new app\assets\MaterialAssetConfig();
    $menu=app\models\Menu::getMenu();
    $img=$config::sidebarBackgroundImage();
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="sidebar" data-color="<?=$config::sidebarColor()  ?>" data-background-color="<?=$config::sidebarBackgroundColor()  ?>">
    <div class="logo">
        <a href="/" class="simple-text logo-mini" alt="<?=\Yii::$app->sys->{"event_name"}?>">
            <img src="<?=$config::logoMini();?>" class="img-fluid rounded" title="<?=\Yii::$app->sys->{"event_name"}?> Logo">
        </a>

        <?php if(!Yii::$app->user->isGuest):?>
          <a href="<?=Url::to(['/profile/me'])?>" class="simple-text logo-normal" style="text-transform:none" title="Profile of <?=Html::encode(Yii::$app->user->identity->username)?>">
            <img src="/images/avatars/<?=Yii::$app->user->identity->profile->avtr;?>" class="img-fluid rounded <?=\app\components\formatters\RankFormatter::ordinalPlaceCss(Yii::$app->user->identity->profile->rank->id)?>" style="max-width: 60px; max-height: 60px" alt="Avatar of <?=Html::encode(Yii::$app->user->identity->username)?>"><br/>
            <?=Html::encode(Yii::$app->user->identity->username)?> <small style="font-size: 0.65em">(<code><?=number_format(Yii::$app->user->identity->profile->score->points)?> pts</code>)</small>
          </a>
          <center id="clock" class="small clock text-primary">Server time: <span id="time"><?=date('H:i');?> <small><?=date_default_timezone_get()?></small></span></center>
        <?php endif;?>
    </div>
    <div class="sidebar-wrapper">
        <?=$menu?>
    </div>
</div>
