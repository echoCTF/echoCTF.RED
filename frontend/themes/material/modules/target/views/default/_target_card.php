<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\Card;
use app\widgets\targetcardactions\TargetCardActions;
use app\modules\game\models\Headshot;
$target_ip=long2ip($target->ip);
$display_ip=Html::a($target_ip,$target_ip,["class"=>'copy-to-clipboard text-dark text-bold','swal-data'=>"Copied to clipboard",'data-toggle'=>'tooltip','title'=>\Yii::t('app',"The IP of the target. Click to copy IP to clipboard.")]);

if($target->on_ondemand && $target->ondemand_state===-1)
{
  $target_ip="0.0.0.0";
  $display_ip=Html::tag('b',$target_ip,['data-toggle'=>'tooltip','title'=>\Yii::t('app',"The IP will be visible once the system is powered up.")]);
}

if(!Yii::$app->user->isGuest && Yii::$app->user->identity->instance !== NULL && Yii::$app->user->identity->instance->target_id===$target->id && Yii::$app->user->identity->instance->player_id===$identity->player_id)
{
  $target_ip=long2ip(Yii::$app->user->identity->instance->ip);
  if(Yii::$app->user->identity->instance->ip===null)
    $display_ip=Html::tag('b',$target_ip,["class"=>'text-danger','data-toggle'=>'tooltip','title'=>\Yii::t('app',"The IP of your private instance will become visible once its powered up.")]);
  else
    $display_ip=Html::a($target_ip,$target_ip,["class"=>'copy-to-clipboard text-danger text-bold','swal-data'=>"Copied to clipboard",'data-toggle'=>'tooltip','title'=>\Yii::t('app',"The IP of your private instance. Click to copy IP to clipboard.")]);
}

$subtitleARR=[$target->category,ucfirst($target->getDifficultyText($target->average_rating)),boolval($target->rootable) ? "Rootable" : "Non rootable",$target->timer===false ? null:'Timed'];
$subtitle=implode(", ",array_filter($subtitleARR));
Card::begin([
            'header'=>'header-icon',
            'type'=>'card-stats',
            'encode'=>false,
            'icon'=>sprintf('<img src="%s" class="img-fluid" style="max-width: 10rem; max-height: 4rem;" />', $target->logo),
            'color'=>'target',
            'subtitle'=>$subtitle,
            'title'=>sprintf('%s / %s', $target->name, $display_ip),
            'footer'=>sprintf('<div class="stats">%s</div><span>%s</span>', $target->purpose, TargetCardActions::widget(['model'=>$target,'identity'=>$identity]) ),
        ]);
echo "<p class='text-danger'><i class='fas fa-flag'></i> ", $target->total_treasures, ": Flag".($target->total_treasures > 1 ? 's' : '')." ";
echo  "<small>(<code class='text-danger'>";
echo $target->treasureCategoriesFormatted;
echo "</code>)</small><br/>";
echo "<i class='fas fa-fire'></i> ", $target->total_findings, ": Service".($target->total_findings > 1 ? 's' : '')."<br/><i class='fas fa-calculator'></i> ", number_format($target->points), " pts";
if($target->timer!==false && $target->timer_avg>0)
  echo '<br/><i class="fas fa-stopwatch"></i> Avg. headshot: '.number_format($target->timer_avg / 60).' minutes';
echo "</p>";
Card::end();?>
