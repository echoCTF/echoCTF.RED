<?php
use app\widgets\Card;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\widgets\Twitter;
use app\modules\game\models\Headshot;
if(date('md')==="0214")
{
  $headshot_icon='fa-heart';
  $noheadshot_icon='fa-heartbeat';
}
else
{
  $headshot_icon='fa-skull-crossbones';
  $noheadshot_icon='fa-not-equal';
}
$player_timer='';
if($target->progress==100) {
  if($target->headshot($identity->player_id)!=null && $target->headshot($identity->player_id)->timer>0)
    $player_timer='<i class="fas fa-stopwatch"></i> '.number_format($target->headshot($identity->player_id)->timer/60).' minutes';
}
?>
<div class="row">
      <div class="col-lg-4 col-md-6 col-sm-6">
<?php /* if($target->headshot($identity->player_id)!==null && $identity->player_id===Yii::$app->user->id)
{
  if($target->headshot($identity->player_id)->rating===-1)
  {
    echo "<p>Hey, don't forget to <b>",
          Html::a('rate me <i class="fas fa-star"></i>',
                  ['/target/default/rate','id' => $target->id],
                  [
                    'title' => 'Rate this target',
                    'data-toggle'=>'modal',
                    'data-target'=>'#modalrate',
                  ]),
                  "</b>.</p>";
 }
}*/
          Card::begin([
            'header'=>'header-icon',
            'type'=>'card-stats',
            'icon'=>sprintf('<img src="%s" class="img-fluid" style="max-width: 10rem; max-height: 4rem;"/>',$target->logo),
            'color'=>'warning',
            'subtitle'=>sprintf("%s, %s", ucfirst($target->difficultyText),boolval($target->rootable) ? "Rootable" : "Non rootable"),
            'title'=>sprintf('%s / %s',$target->name,long2ip($target->ip)),
            'footer'=>sprintf('<div class="stats">%s</div>',$target->purpose),
        ]);
        echo "<p class='text-danger'><i class='fas fa-flag'></i> ",$target->total_treasures,": Flag".($target->total_treasures>1?'s':'')."<br/>";
        echo  "<small>(<code class='text-danger'>";
        echo $target->treasureCategoriesFormatted;
        echo "</code>)</small><br/>";
        echo "<i class='fas fa-fire'></i> ", $target->total_findings,": Service".($target->total_findings>1?'s':'')."<br/><i class='fas fa-calculator'></i> ",number_format($target->points), " pts";
        $hs=Headshot::find()->target_avg_time($target->id)->one();
        if($hs && $hs->average>0)
          echo '<br/><i class="fas fa-stopwatch"></i> Avg. headshot: '.number_format($hs->average/60).' minutes';
        echo "</p>";
        Card::end(); ?>
      </div>
      <div class="col-lg-4 col-md-6 col-sm-6">
        <div  style="line-height: 1.5; font-size: 7vw; vertical-align: bottom; text-align: center;" class="<?=$target->progress==100? 'text-primary':'text-danger'?>">
          <i class="fa <?=$target->progress==100 ? $headshot_icon:$noheadshot_icon?>"></i>
        </div>
        <div class="progress">
            <div class="progress-bar <?=$target->progress==100 ? 'bg-gradual-progress':'bg-danger text-dark'?>" style="width: <?=$target->progress?>%" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                <?=$target->progress==100 ? '#headshot': number_format($target->progress).'%'?>
            </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 col-sm-6">
        <?php Card::begin([
            'header'=>'header-icon',
            'type'=>'card-stats',
            'icon'=>sprintf('<img src="/images/avatars/%s" height="60"/>',$identity->avatar),
            'color'=>'primary',
            'subtitle'=>'Level '.$identity->experience->id.' / '.$identity->experience->name,
            'title'=>$identity->owner->username." / ".$identity->rank->ordinalPlace." Place",
            'footer'=>sprintf('<div class="stats">%s %s</div>', Twitter::widget([
                            'message'=>sprintf('Hey check this out, %s have found %d out of %d services and %d out of %d flags on [%s]', $identity->isMine ? "I" : $identity->twitterHandle,$target->player_findings,$target->total_findings,$target->player_treasures,$target->total_treasures,$target->name),
                            /*'url'=>Url::to(['/target/default/index'*,'id'=>$target->id],'https'),*/
                            'linkOptions'=>['class'=>'target-view-tweet','target'=>'_blank','style'=>'font-size: 1.4em;'],
                        ]),Html::encode($identity->bio)),
        ]);
        echo "<p class='text-primary '><i class='fas fa-flag-checkered'></i> ", $target->player_treasures,": Flags found<br/>";
        echo '<i class="fas fa-fire-alt"></i> ', $target->player_findings,": Service".($target->player_findings>1?'s':'')." discovered<br/>";
        echo '<i class="fas fa-calculator"></i> ',number_format($playerPoints)," pts<br/>";
        echo $player_timer;
        Card::end(); ?>
      </div>
</div>
  <div class="row">
    <div class="col-lg-8 col-md-6 col-sm-6">
      <div class="card bg-dark">
        <div class="card-body table-responsive">
          <?=$target->description?>
          <?php if(!Yii::$app->user->isGuest && Yii::$app->user->id===$identity->player_id):?>
          <?php if($identity->owner->getPlayerHintsForTarget($target->id)->count()>0) echo "<br/><i class='fas fa-smile-wink'></i> <code>", implode(', ',ArrayHelper::getColumn($identity->owner->getPlayerHintsForTarget($target->id)->all(),'hint.title')),"</code>";?>
          <?php endif;?>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6">
      <div class="card bg-dark headshots">
        <h4><i class="fas fa-skull"></i> Headshots (older first)</h4>
        <div class="card-body table-responsive">
          <?php
          $headshots=[];
          foreach($target->headshots as $hs)
          {
            if((int)$hs->player->active===1)
              $headshots[]=$hs->player->profile->link;
          }
          if (!empty($headshots))
            echo "<code>",implode(", ",$headshots), "</code>";
          else
            echo '<code>none yet...</code>';?>
        </div>
      </div>
    </div>
  </div>
