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

if($target->ondemand && $target->ondemand->state<0)
{
  $spinlink=Html::a(
    '<i class="fas fa-play" style="font-size: 2em; float:left"></i>',
      Url::to(['/target/default/spin', 'id'=>$target->id]),
      [
        'style'=>"font-size: 1.0em;",
        'class'=>'text-danger',
        'title' => 'Start the target',
        'rel'=>"tooltip",
        'data-pjax' => '0',
        'data-method' => 'POST',
        'aria-label'=>'Start the target',
      ]
  );

}
else
{
$spinlink=Html::a(
  '<i class="fas fa-power-off" style="font-size: 2em; float:left"></i>',
    Url::to(['/target/default/spin', 'id'=>$target->id]),
    [
      'style'=>"font-size: 1.0em;",
      'title' => 'Request target Restart',
      'rel'=>"tooltip",
      'data-pjax' => '0',
      'data-method' => 'POST',
      'aria-label'=>'Request target Restart',
    ]
);
}
if(date('md') === "0214")
{
  $headshot_icon='fa-heart';
  $noheadshot_icon='fa-heartbeat';
}
elseif(date('md')==="1014")
{
  $headshot_icon='fa-birthday-cake';
  $noheadshot_icon='fa-candy-cane';
}
else
{
  $headshot_icon='fa-skull-crossbones';
  $noheadshot_icon='fa-not-equal';
}
$player_timer='';
$twmsg=sprintf('Hey check this out, %s have found %d out of %d services and %d out of %d flags on [%s]', $identity->isMine ? "I" : $identity->twitterHandle, $target->player_findings, $target->total_findings, $target->player_treasures, $target->total_treasures, $target->name);
if($target->progress == 100)
{
  if($target->headshot($identity->player_id) != null && $target->headshot($identity->player_id)->timer > 0)
  {
    $player_timer='<i class="fas fa-stopwatch"></i> '.number_format($target->headshot($identity->player_id)->timer / 60).' minutes';
    $twmsg=sprintf('Hey check this out, %s managed to headshot [%s] in %d minutes', $identity->isMine ? "I" : $identity->twitterHandle, $target->name,$target->headshot($identity->player_id)->timer/60);
  }
  else
    $twmsg=sprintf('Hey check this out, %s managed to headshot [%s]', $identity->isMine ? "I" : $identity->twitterHandle, $target->name);
}
$headshot=Headshot::findOne(['player_id'=>$identity->player_id, 'target_id'=>$target->id]);
?>
<div class="row">
      <div class="col-xl-4 col-lg-5 col-md-5 col-sm-12 target-card">
<?php Card::begin([
            'header'=>'header-icon',
            'type'=>'card-stats',
            'icon'=>sprintf('<img src="%s" class="img-fluid" style="max-width: 10rem; max-height: 4rem;"/>', $target->logo),
            'color'=>'warning',
            'subtitle'=>sprintf("%s, %s%s", ucfirst($target->difficultyText), boolval($target->rootable) ? "Rootable" : "Non rootable",$target->timer===0 ? '':', Timed'),
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
      </div>
      <div class="col-xl-4 col-lg-2 col-md-2 col-sm-12 text-center">
        <?php if($headshot!==null && $headshot->first):?>
          <img src="/images/1stheadshot.svg" class="img-fluid" style="max-height: 200px">
        <?php else:?>
        <div style="line-height: 1.5; font-size: 7vw; vertical-align: bottom; text-align: center;" class="<?=$target->progress == 100 ? 'text-primary' : 'text-danger'?>">
          <i class="fa <?=$target->progress == 100 ? $headshot_icon : $noheadshot_icon?>"></i>
        </div>
        <?php endif;?>
        <div class="progress">
          <div class="progress-bar <?=$target->progress == 100 ? 'bg-gradual-progress' : 'bg-danger text-dark'?>" style="width: <?=$target->progress?>%" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
              <?=$target->progress == 100 ? '#headshot' : number_format($target->progress).'%'?>
          </div>
        </div>
        <?php if($headshot && $headshot->rating>=0) echo "<center class='text-primary'>headshoter rating: ",$headshot->rated,"</center>";?>
        <?php if(Yii::$app->user->id === $identity->player_id && Writeup::findOne(['player_id'=>$identity->player_id, 'target_id'=>$target->id])===null && $target->progress==100):?>
        <div class="row">
          <div class="col">
          <?=Html::a("<i class='fas fa-book'></i> Submit",
                      ['writeup/submit','id'=>$target->id],
                      [
                        'title' => 'Submit a writeup for this target',
                        'rel'=>"tooltip",
                        'aria-label'=>'Submit a writeup for this target',
                        'class'=>'btn',
                        'style'=>'width: 100%',
                        'alt'=>'Submit a writeup for this target'
                    ])?>
          </div>
          <div class="col">
            <?=VoteWidget::widget(['model'=>$headshot,'id'=>$headshot->target_id,'action'=>'/game/default/rate-headshot']);?>
          </div>
        </div>
        <?php elseif(Yii::$app->user->id === $identity->player_id && $target->progress==100):?>
        <div class="row">
          <div class="col">
          <?=Html::a("<i class='fas fa-book'></i> View",
                      ['writeup/view','id'=>$target->id],
                      [
                        'title' => 'View or update your writeup for this target',
                        'rel'=>"tooltip",
                        'aria-label'=>'View or update your writeup for this target',
                        'class'=>'btn btn-info',
                        'alt'=>'View or update your writeup for this target'
                    ])?>
          </div>
          <div class="col">
            <?=VoteWidget::widget(['model'=>$headshot,'id'=>$headshot->target_id,'action'=>'/game/default/rate-headshot']);?>
          </div>
        </div>
        <?php endif;?>
      </div>
      <div class="col-xl-4 col-lg-5 col-md-5 col-sm-12">
        <?php Card::begin([
            'header'=>'header-icon',
            'type'=>'card-stats',
            'icon'=>sprintf('<img src="/images/avatars/%s" height="60"/>', $identity->avtr),
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
        echo $player_timer,"<br/>";
        Card::end();?>
      </div>
</div>
  <div class="row">
    <div class="col">
      <div class="card terminal">
        <div class="card-body">
          <?php if(count($target->writeups)>0 && PTH::findOne(['player_id'=>Yii::$app->user->id,'target_id'=>$target->id])===null && !($identity->player_id===Yii::$app->user->id && $target->progress==100)):?>
          <?=Html::a(
            '<i class="fas fa-question-circle" style="font-size: 1.5em;"></i> Writeups available.',
              ['/target/writeup/enable', 'id'=>$target->id],
              [
                'style'=>"font-size: 1.0em;",
                'title' => 'Request access to writeups',
                'rel'=>"tooltip",
                'data-pjax' => '0',
                'data-method' => 'POST',
                'data-confirm'=>'Are you sure you want to enable access to writeups for this target? Any remaining flags will have their points reduced by 50%.',
                'aria-label'=>'Request access to writeups',
              ]
          )?><br/><?php endif;?>
          <?=$target->description?>
          <?php if(!Yii::$app->user->isGuest && Yii::$app->user->id === $identity->player_id):?>
            <?php if(Yii::$app->user->identity->getPlayerHintsForTarget($target->id)->count() > 0) echo "<br/><i class='fas fa-lightbulb text-success'></i> <code class='text-success'>", implode(', ', ArrayHelper::getColumn($identity->owner->getPlayerHintsForTarget($target->id)->all(), 'hint.title')), "</code>";?>
          <?php endif;?>
        </div>
      </div>

      <?php if(count($target->writeups)>0 && (PTH::findOne(['player_id'=>Yii::$app->user->id,'target_id'=>$target->id])!==null || ($identity->player_id===Yii::$app->user->id && $target->progress==100))):?>
      <div class="card terminal">
        <div class="card-body table-responsive">
          <h4><i class="fas fa-book"></i> Target Writeups</h4>
          <?php foreach($target->writeups as $writeup):?>
            <p><details><summary><b style="font-size: 1.2em;">Writeup by <?=Html::encode($writeup->player->username)?>, submitted <?=$writeup->created_at?></b> (<code>status:<?=$writeup->status?></code>)</summary>
              <div class="markdown"><?=Markdown::process($writeup->content,'gfm')?></div>
            </details></p>
          <?php endforeach;?>
        </div>
      </div>
      <?php endif;?>


<?php if(!Yii::$app->user->isGuest && Yii::$app->user->id === $identity->player_id && (Yii::$app->user->identity->getFindings($target->id)->count()>0 || Yii::$app->user->identity->getTreasures($target->id)->count()>0)):?>
      <div class="card terminal">
        <div class="card-body">
          <pre style="font-size: 0.9em;">
<?php
          if(Yii::$app->user->identity->getFindings($target->id)->count()>0) echo '# Discovered services',"\n";
          foreach(Yii::$app->user->identity->getFindings($target->id)->all() as $finding)
          {
            printf("* %s://%s:%d\n",$finding->protocol,long2ip($target->ip),$finding->port);
          }

          if(Yii::$app->user->identity->getTreasures($target->id)->count()>0) echo "\n",'# Discovered flags',"\n";
          foreach(Yii::$app->user->identity->getTreasures($target->id)->orderBy(['id' => SORT_DESC])->all() as $treasure)
          {
            printf("* (%s/%d pts) %s\n",$treasure->category,$treasure->points,$treasure->location);
            //if(trim($treasure->solution)!=='') echo $treasure->solution,"\n";
          }
?></pre>
        </div>
      </div>
    <?php endif;?>

    </div>
    <div class="col-lg-4 col-md-6 col-sm-6">
      <div class="card bg-dark headshots">
        <div class="card-header">
          <h4><i class="fas fa-skull"></i> Headshots (older first)</h4>
        </div>
        <div class="card-body table-responsive">
          <?php
          $headshots=[];
          foreach($target->headshots as $hs)
          {
            if((int) $hs->player->active === 1)
              $headshots[]=$hs->player->profile->link;
          }
          if(!empty($headshots))
          {
            echo "<code>",implode(", ", array_slice($headshots, 0,19)),"</code>";
            if(count($headshots)>19){
              echo "<details class=\"headshotters\">";
              echo "<summary data-open=\"Hide more\" data-close=\"Show more\"></summary>";
              echo "<code>",implode(", ", array_slice($headshots, 19)),"</code>";
              echo "</details>";
            }
          }
          else
            echo '<code>none yet...</code>';?>
        </div>
      </div>
    </div>
  </div>
