<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
?>
<div class="card terminal">
  <div class="card-body">
    <pre style="font-size: 0.9em;">
<?php
    if(Yii::$app->user->identity->getFindings($target->id)->count()>0) echo '# Discovered services',"\n";
    foreach(Yii::$app->user->identity->getFindings($target->id)->all() as $finding)
    {
      printf("* %s://%s:%d\n",$finding->protocol,long2ip($target->ip),$finding->port);
      if($finding->hints!=[])
      {
        echo " <i class='fas fa-lightbulb text-success'></i> <code class='text-success'>", implode(', ', ArrayHelper::getColumn($finding->hints, 'title')), "</code>";
      }
    }
    if(Yii::$app->user->identity->getTreasures($target->id)->count()>0) echo "\n",'# Discovered flags',"\n";
    foreach(Yii::$app->user->identity->getTreasures($target->id)->orderBy(['id' => SORT_DESC])->all() as $treasure)
    {
      printf("* (%s/%d pts) %s\n",$treasure->category,$treasure->points,$treasure->location);
      if($treasure->hints!=[])
      {
        echo " <i class='fas fa-lightbulb text-success'></i> <code class='text-success'>", implode(', ', ArrayHelper::getColumn($treasure->hints, 'title')), "</code>";
      }
    }
?></pre>
  </div>
</div>
