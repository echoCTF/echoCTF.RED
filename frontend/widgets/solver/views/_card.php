<?php
use yii\helpers\Html;
?>
<div class="<?=$htmlOptions['class']?>">
  <h4><i class="fas fa-tasks"></i> <?=$solvers->count()?> Solvers (older first)</h4>
  <div class="card-body table-responsive"><?php
    $solves=[];
    foreach($solvers->orderBy(['created_at'=>SORT_ASC, 'player_id'=>SORT_ASC])->all() as $solver)
    {
      if((int) $solver->player->active === 1)
        $solves[]=$solver->player->profile->link;
    }
    if(!empty($solves))
    {
      echo "<code>",implode(", ", array_slice($solves, 0,$slice)),"</code>";
      if(count($solves)>19){
        echo "<details class=\"headshotters\">";
        echo "<summary data-open=\"Hide more\" data-close=\"Show more\"></summary>";
        echo "<code>",implode(", ", array_slice($solves, $slice)),"</code>";
        echo "</details>";
      }
    }
    else
      echo '<code>none yet...</code>';?>
  </div>
</div>
