<?php
use yii\helpers\Html;
$tick='<svg class="bi bi-check" width="1.4em" height="1.4em" viewBox="0 0 16 16" fill="green" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/></svg> ';
$ex='<svg class="bi bi-x" width="1.4em" height="1.4em" viewBox="0 0 16 16" fill="red" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" d="M11.854 4.146a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708-.708l7-7a.5.5 0 0 1 .708 0z"/>
<path fill-rule="evenodd" d="M4.146 4.146a.5.5 0 0 0 0 .708l7 7a.5.5 0 0 0 .708-.708l-7-7a.5.5 0 0 0-.708 0z"/>
</svg> ';
$this->title="Progress: ".Html::encode($model->player->username)." vs ".Html::encode($model->target->name);

?>
<h2><?=$this->title?></h2>
<p>You have entered the following information:</p>

<ul>
    <li><label>Target</label>: <b><code><?= Html::encode($model->target->fqdn) ?> / <?= Html::encode($model->target->ipoctet) ?></code></b></li>
    <li><label>Player</label>: <?= Html::encode($model->player->username) ?></li>
</ul>
<?php

foreach($model->target->findings as $finding)
{
  $solved=$ex;
  if($model->hasFinding($finding->id))
    $solved=$tick;
  echo $finding->port,"/", $finding->protocol," ",$finding->name,"{$solved}<br/>";
}

foreach($model->target->treasures as $treasure)
{
  $solved=$ex;
  if($model->hasTreasure($treasure->id))
  $solved=$tick;
  echo $treasure->category," <code>[{$treasure->location}]</code> ", $treasure->name," ",$treasure->points,"{$solved}<br/>";
}
?>
