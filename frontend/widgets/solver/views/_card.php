<?php
use yii\helpers\Html;
?>
<div class="<?=$htmlOptions['class']?>">
  <h4><i class="fas fa-tasks"></i> <?=count($solvers)>0? count($solvers)." " : ""?>Solvers <small>(newer first)</small></h4>
  <div class="card-body table-responsive"><?php
    if(!empty($solvers))
    {
      echo "<code>",implode(", ", array_slice($solvers, 0,$slice)),"</code>";
      if(count($solvers)>19){
        echo "<details class=\"headshotters\">";
        echo "<summary data-open=\"Hide more\" data-close=\"Show more\"></summary>";
        echo "<code>",implode(", ", array_slice($solvers, $slice)),"</code>";
        echo "</details>";
      }
    }
    else
      echo '<code>none yet...</code>';?>
  </div>
</div>
