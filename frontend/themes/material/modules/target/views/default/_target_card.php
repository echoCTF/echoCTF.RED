<?php
use app\widgets\Card;
use app\modules\game\models\Headshot;

$subtitleARR=[$target->category,ucfirst($target->difficultyText),boolval($target->rootable) ? "Rootable" : "Non rootable",$target->timer===0 ? null:'Timed'];
$subtitle=implode(", ",array_filter($subtitleARR));
Card::begin([
            'header'=>'header-icon',
            'type'=>'card-stats',
            'icon'=>sprintf('<img src="%s" class="img-fluid" style="max-width: 10rem; max-height: 4rem;" />', $target->logo),
            'color'=>'warning',
            'subtitle'=>$subtitle,
            'title'=>sprintf('%s / %s', $target->name, long2ip($target->ip)),
            'footer'=>sprintf('<div class="stats">%s</div><span>%s</span>', $target->purpose,  !Yii::$app->user->isGuest && $target->spinable ? $spinlink:""),
        ]);
echo "<p class='text-danger'><i class='fas fa-flag'></i> ", $target->total_treasures, ": Flag".($target->total_treasures > 1 ? 's' : '')."<br/>";
echo  "<small>(<code class='text-danger'>";
echo $target->treasureCategoriesFormatted;
echo "</code>)</small><br/>";
echo "<i class='fas fa-fire'></i> ", $target->total_findings, ": Service".($target->total_findings > 1 ? 's' : '')."<br/><i class='fas fa-calculator'></i> ", number_format($target->points), " pts";
$hs=Headshot::find()->target_avg_time($target->id)->one();
if($hs && $hs->average > 0 && $target->timer!==0)
  echo '<br/><i class="fas fa-stopwatch"></i> Avg. headshot: '.number_format($hs->average / 60).' minutes';
echo "</p>";
Card::end();?>
