<?php
use rce\material\widgets\Card;
use yii\helpers\Html;
?>
<div class="row">
      <div class="col-lg-4 col-md-6 col-sm-6">
        <?php Card::begin([
            'header'=>'header-icon',
            'type'=>'card-stats',
            'icon'=>sprintf('<img src="/images/targets/_%s.png" class="img-fluid" style="max-width: 10rem; max-height: 4rem;"/>',$target->name),
            'color'=>'warning',
            'subtitle'=>sprintf("%s, %s", $target->difficultyText,boolval($target->rootable) ? "rootable" : "non rootable"),
            'title'=>sprintf('%s / %s',$target->name,long2ip($target->ip)),
            'footer'=>sprintf('<div class="stats">%s</div>',$target->purpose),
        ]);
        echo "<p class='text-danger'><i class='material-icons'>flag</i> ", $target->total_treasures," / ";
        echo "<i class='material-icons'>whatshot</i> ", $target->total_findings,"<br/>",number_format($target->points), " pts</p>";
        Card::end(); ?>
      </div>
      <div class="col-lg-4 col-md-6 col-sm-6">
        <div  style="line-height: 1.5; font-size: 7vw; vertical-align: bottom; text-align: center;" class="<?=$target->progress==100? 'text-primary':'text-danger'?>">
          <i class="fa <?=$target->progress==100 ? 'fa-skull-crossbones':'fa-not-equal'?>"></i>
        </div>
        <div class="progress">
            <div class="progress-bar <?=$target->progress==100 ? 'bg-gradual-progress':'bg-danger text-dark'?>" style="width: <?=$target->progress?>%" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"><?=$target->progress==100 ? '#Headshot': number_format($target->progress).'%'?></div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 col-sm-6">
        <?php Card::begin([
            'header'=>'header-icon',
            'type'=>'card-stats',
            'icon'=>sprintf('<img src="/images/avatars/%s" height="60"/>',Yii::$app->user->identity->profile->avatar),
            'color'=>'primary',
            'subtitle'=>Html::encode('Level '.Yii::$app->user->identity->profile->experience->id.' / '.Yii::$app->user->identity->profile->experience->name),
            'title'=>Html::encode(Yii::$app->user->identity->username)." / ".Yii::$app->user->identity->profile->rank->ordinalPlace." Place",
            'footer'=>sprintf('<div class="stats">%s</div>',Html::encode(Yii::$app->user->identity->profile->bio)),
        ]);
        echo "<p class='text-primary '><i class='material-icons'>flag</i> ", $target->player_treasures," / ";
        echo "<i class='material-icons'>whatshot</i> ", $target->player_findings,"<br/>",number_format($playerPoints)," pts<br/>";
        Card::end(); ?>
      </div>
</div>
  <div class="row">
    <div class="col-lg-8 col-md-6 col-sm-6">
      <div class="card bg-dark">
        <div class="card-body table-responsive">
          <?=$target->description?>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6">
      <div class="card bg-dark headshots">
        <h4><i class="fas fa-skull"></i> Headshots by</h4>
        <div class="card-body table-responsive">
          <?php
          $headshots=null;
          foreach($target->headshots as $player)
            if((int)$player->active===1)
              $headshots[]=$player->profile->link;
            if ($headshots!==NULL)
              echo "<code>",implode(", ",$headshots), "</code>";
            else echo '<code>none yet...</code>';?>
        </div>
      </div>
    </div>
  </div>
