
<h1>Completion Certificate</h1>
<div class="watermarked img-fluid">
  <img class="card-img-top" src="<?=$headshot->target->logo?>" alt="Card image cap" width="100px">
</div>
<h2><b><?=$headshot->player->username?></b></h2>
<p class="card-text">  <small>has completed the target</small>
  <b><?=$headshot->target->name?></b>
  <?php if($headshot->target->timer):?>
    in <?=$headshot->timer?> seconds<?php if(array_search($top[0],$headshot->player_id)!==false):?> (best time)<?php endif;?>
  <?php endif;?>
</p>

<a href="#" class="btn btn-primary">Go somewhere</a>
