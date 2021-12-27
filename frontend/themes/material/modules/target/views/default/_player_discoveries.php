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
