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
          <a href="<?=Url::to(['/profile/me'])?>" class="simple-text logo-normal" style="text-transform:none" title="<?=\Yii::t('app','Profile of')?> <?=Html::encode(Yii::$app->user->identity->username)?><?= Yii::$app->user->identity->onVPN ? "\nVPN IP: ".Yii::$app->user->identity->vpnIP : "" ?>">
            <?php if(Yii::$app->user->identity->isVip && Yii::$app->sys->all_players_vip===false):?>
                <span class="badge badge-danger" style="position: absolute; bottom: 32%; left: 57%"><?php if(Yii::$app->user->identity->subscription!==null):?><img src="/images/<?=Yii::$app->user->identity->subscription->product ? Yii::$app->user->identity->subscription->product->shortcode : "vip"?>.svg" width="20px"><?php else:?>VIP<?php endif;?></span>
            <?php endif;?>
            <img style="width: 75px; height: 75px" src="/images/avatars/<?=Yii::$app->user->identity->profile->avtr;?>?<?=Yii::$app->formatter->asTimestamp(Yii::$app->user->identity->profile->updated_at)?>" class="img-fluid rounded <?php if(Yii::$app->user->identity->isVip):?>border-danger<?php endif;?> <?=\app\components\formatters\RankFormatter::ordinalPlaceCss(Yii::$app->user->identity->profile->rank->id)?>" style="max-width: 60px; max-height: 60px" alt="Avatar of <?=Html::encode(Yii::$app->user->identity->username)?>"><br/>
            <i class="fas fa-shield-alt <?= Yii::$app->user->identity->onVPN ? "text-primary" : "text-danger"?>" style="font-size: 0.75em"></i> <?=Html::encode(Yii::$app->user->identity->username)?> <small style="font-size: 0.65em">(<code><?=number_format(Yii::$app->user->identity->profile->score->points)?> pts</code>)</small>
          </a>
          <center id="clock" class="small clock text-primary"><?=\Yii::t('app','Server time:')?> <span id="time"><?=date('H:i');?><?php if(\Yii::$app->sys->hide_timezone!==true):?> <small><?=date_default_timezone_get()?></small><?php endif;?></span></center>
        <?php endif;?>
    </div>
    <div class="sidebar-wrapper">
        <?=$menu?>
    </div>
</div>
