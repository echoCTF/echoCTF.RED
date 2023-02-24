<?php
use yii\helpers\Html;

?>
<div class="panel">
  <div class="panel-heading">
    <span class="panel-icon">
      <i class="fas fa-info-circle"></i>
    </span>
    <span class="panel-title"> Counters</span>
  </div>
  <div class="panel-body pn">
    <table class="table mbn tc-icon-1 tc-med-2 tc-bold-last">
      <thead>
        <tr class="hidden">
        </tr>
      </thead>
      <tbody>
        <?php foreach($model->owner->countersNf as $nf):?>
          <tr>
          <td><i class="fas fa-nf-<?=$nf->metric?>"></i></td>
          <td><?=$nf->metric?></td>
          <td><?=$nf->counter?></td>
          </tr>
        <?php endforeach;?>
        <tr>
          <td><i class="fas fa-skull"></i></td>
          <td>Headshots</td>
          <td><?=Html::encode(count($model->owner->headshots))?></td>
        </tr>
        <tr>
          <td><i class="fas fa-tasks"></i></td>
          <td>Challenges</td>
          <td><?=Html::encode(count($model->owner->challengeSolvers))?></td>
        </tr>
        <tr>
          <td><i class="fas fa-power-off"></i></td>
          <td>Spin counter</td>
          <td><?=Html::encode($model->owner->playerSpin->counter)?></td>
        </tr>
        <tr>
          <td><i class="fas fa-retweet"></i></td>
          <td>Spins per day</td>
          <td><?=Html::encode($model->owner->playerSpin->perday)?></td>
        </tr>
        <tr>
          <td><i class="fas fa-sync-alt"></i></td>
          <td>Spins overall</td>
          <td><?=Html::encode($model->owner->playerSpin->total)?></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
