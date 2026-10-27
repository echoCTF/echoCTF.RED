<?php
/**
 * On-demand power state of a target (powered off message or shutdown countdown).
 * Rendered by view.php and returned by /target/<id>/ip so the block can be refreshed live.
 * The countdown is driven by initTargetCountdown() in libechoctf.js.
 * @var $target app\modules\target\models\Target
 */
if(\app\modules\target\Module::ondemandDisabled() || !$target->ondemand) return;
?>
<?php if($target->ondemand->state<0):?>
    <div><p class="text-info"><?=\Yii::t('app','This target is currently powered off.')?> <?php if(Yii::$app->user->identity->profile->last->vpn_local_address===null):?><em><?=\Yii::t('app','Connect to the VPN to be allowed to power the system up.')?></em><?php endif;?></p></div>
<?php elseif($target->ondemand->state>0):?>
    <div><p class="text-danger"><?=\Yii::t('app','The target will shutdown in')?> <code id="tcountdown" data="<?=intval($target->ondemand->expired)?>" data-expired-text="<?=\yii\helpers\Html::encode(\Yii::t('app','system will shutdown soon!'))?>"></code></p></div>
<?php endif;?>
