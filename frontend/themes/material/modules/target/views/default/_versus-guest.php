<?php
use app\widgets\Card;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\widgets\Twitter;
use app\widgets\vote\VoteWidget;
use app\modules\game\models\Headshot;
use app\modules\target\models\PlayerTargetHelp as PTH;
use app\modules\target\models\Writeup;
use yii\helpers\Markdown;
$this->loadLayoutOverrides=true;
$spinlink=null;
$target_actions=null;
$player_timer='';
$twmsg=sprintf('Hey check this out, %s have found %d out of %d services and %d out of %d flags on [%s]', $identity->twitterHandle, $target->player_findings, $target->total_findings, $target->player_treasures, $target->total_treasures, $target->name);
if($target->progress == 100)
{
  if($target->headshot($identity->player_id) != null && $target->headshot($identity->player_id)->timer > 0)
  {
    $player_timer='<i class="fas fa-stopwatch"></i> '.number_format($target->headshot($identity->player_id)->timer / 60).' minutes';
    $twmsg=sprintf('Hey check this out, %s managed to headshot [%s] in %d minutes', $identity->twitterHandle, $target->name,$target->headshot($identity->player_id)->timer/60);
  }
  else
    $twmsg=sprintf('Hey check this out, %s managed to headshot [%s]', $identity->twitterHandle, $target->name);
}
$headshot=Headshot::findOne(['player_id'=>$identity->player_id, 'target_id'=>$target->id]);
if($headshot)
{
  $this->registerMetaTag(['name'=>'og:type', 'content'=>'game.achievement']);
  $this->registerMetaTag(['name'=>'game:points', 'content'=>'0']);
  $this->registerMetaTag(['name'=>'article:published_time', 'content'=>$headshot->created_at]);
}
?>
  <div class="row">
      <div class="col-xl-4 col-lg-5 col-md-5 col-sm-12 target-card">
        <?=$this->render('_target_card', ['target'=>$target,'spinlink'=>$spinlink,'target_actions'=>$target_actions,'identity'=>$identity]);?>
      </div>
      <div class="col-xl-4 col-lg-2 col-md-2 col-sm-12 text-center">
        <?php if($headshot!==null && $headshot->first):?>
          <img src="/images/1stheadshot.svg" class="img-fluid" style="max-height: 200px">
        <?php else:?>
        <div style="line-height: 1.5; font-size: 7vw; vertical-align: bottom; text-align: center;" class="<?=$target->progress == 100 ? 'text-primary' : 'text-danger'?>">
          <span class="<?=$target->progress == 100 ? "vscomplete" : "vsincomplete"?>"></span>
        </div>
        <?php endif;?>
        <div class="progress">
          <div class="progress-bar <?=$target->progress == 100 ? 'bg-gradual-progress' : 'bg-danger text-dark'?>" style="width: <?=$target->progress?>%" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
              <?=$target->progress == 100 ? '#headshot' : number_format($target->progress).'%'?>
          </div>
        </div>
        <?php
        if($headshot && $headshot->rating>=0) { echo "<center class='text-primary'>headshoter rating: ",$headshot->rated,"</center>";}
        ?>
      </div>
      <div class="col-xl-4 col-lg-5 col-md-5 col-sm-12">
        <?php Card::begin([
            'header'=>'header-icon',
            'type'=>'card-stats',
            'icon'=>sprintf('<img class="rounded" src="/images/avatars/%s" height="60"/>', $identity->avtr),
            'color'=>'primary',
            'subtitle'=>'Level '.$identity->experience->id.' / '.$identity->experience->name,
            'title'=>$identity->owner->username." / ".$identity->rank->ordinalPlace." Place",
            'footer'=>sprintf('<div class="stats">%s %s</div>', Twitter::widget([
                            'message'=>$twmsg,
                            /*'url'=>Url::to(['/target/default/view'*,'id'=>$target->id],'https'),*/
                            'linkOptions'=>['class'=>'target-view-tweet', 'target'=>'_blank', 'style'=>'font-size: 1.4em;', 'rel'=>'noopener noreferrer nofollow'],
                        ]), Html::encode($identity->bio)),
        ]);
        echo "<p class='text-primary '><i class='fas fa-flag-checkered'></i> ", $target->player_treasures, ": Flags found<br/>";
        echo '<i class="fas fa-fire-alt"></i> ', $target->player_findings, ": Service".($target->player_findings > 1 ? 's' : '')." discovered<br/>";
        echo '<i class="fas fa-calculator"></i> ', number_format($playerPoints), " pts<br/>";
        if($player_timer)
          echo $player_timer,"<br/>";
        echo "</p>";
        Card::end();?>
      </div>
  </div>
