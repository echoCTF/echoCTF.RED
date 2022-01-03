<div class="card bg-dark headshots">
  <div class="card-header">
    <h4><i class="fas fa-skull"></i> <?=count($target->headshots)>0 ? count($target->headshots)." " : ""?>Headshots (newer first)</h4>
  </div>
  <div class="card-body table-responsive">
    <?php
    $headshots=[];
    foreach($target->lastHeadshots as $hs)
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
