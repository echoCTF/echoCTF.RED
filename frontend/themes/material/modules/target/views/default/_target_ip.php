<?php
/**
 * IP element shown on the target card title.
 * Rendered by _target_card.php and returned by /target/<id>/ip so the card can be refreshed live.
 * @var $target app\modules\target\models\Target
 * @var $identity app\models\Profile
 */
use yii\helpers\Html;
$target_ip=long2ip((int) $target->ip);
$display_ip=Html::a($target_ip,$target_ip,["class"=>'copy-to-clipboard text-dark text-bold','swal-data'=>"Copied to clipboard",'data-toggle'=>'tooltip','title'=>\Yii::t('app',"The IP of the target. Click to copy IP to clipboard.")]);

if($target->on_ondemand && \app\modules\target\Module::instancesOnly())
{
  $target_ip="0.0.0.0";
  $display_ip=Html::tag('b',$target_ip,['data-toggle'=>'tooltip','title'=>\Yii::t('app',"Spawn a private instance of this target to get an IP.")]);
}
elseif($target->on_ondemand && $target->ondemand_state===-1)
{
  $target_ip="0.0.0.0";
  $display_ip=Html::tag('b',$target_ip,['data-toggle'=>'tooltip','title'=>\Yii::t('app',"The IP will be visible once the system is powered up.")]);
}

if(!Yii::$app->user->isGuest && Yii::$app->user->identity->instance !== NULL && Yii::$app->user->identity->instance->target_id===$target->id && Yii::$app->user->identity->instance->player_id===$identity->player_id)
{
  $target_ip=long2ip((int) Yii::$app->user->identity->instance->ip);
  if(Yii::$app->user->identity->instance->ip===null)
    $display_ip=Html::tag('b',$target_ip,["class"=>'text-danger','data-toggle'=>'tooltip','title'=>\Yii::t('app',"The IP of your private instance will become visible once its powered up.")]);
  else
    $display_ip=Html::a($target_ip,$target_ip,["class"=>'copy-to-clipboard text-danger text-bold','swal-data'=>"Copied to clipboard",'data-toggle'=>'tooltip','title'=>\Yii::t('app',"The IP of your private instance. Click to copy IP to clipboard.")]);
}
echo $display_ip;
