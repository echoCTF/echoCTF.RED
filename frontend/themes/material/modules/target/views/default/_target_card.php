<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\Card;
use app\widgets\targetcardactions\TargetCardActions;
use app\modules\game\models\Headshot;
$display_ip=$this->render('_target_ip',['target'=>$target,'identity'=>$identity]);
$subtitleARR=[$target->category,ucfirst($target->getDifficultyText($target->average_rating)),boolval($target->rootable) ? "Rootable" : "Non rootable",$target->timer===false ? null:'Timed'];
$subtitle=implode(", ",array_filter($subtitleARR));
Card::begin([
            'header'=>'header-icon',
            'type'=>'card-stats',
            'encode'=>false,
            'icon'=>sprintf('<img src="%s" class="img-fluid" style="max-width: 10rem; max-height: 4rem;" />', $target->logo),
            'color'=>'target',
            'subtitle'=>$subtitle,
            'title'=>sprintf('%s / <span id="target_id_%d">%s</span>', $target->name, $target->id, $display_ip),
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
