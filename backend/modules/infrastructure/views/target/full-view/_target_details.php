<?php

use app\widgets\BooleanTransform as BT;
?>
<div class="panel">
  <div class="panel-heading">
    <span class="panel-icon">
      <i class="fas fa-diagnoses"></i>
    </span>
    <span class="panel-title"> Details</span>
  </div>
  <div class="panel-body pn">
    <table class="table mbn tc-icon-1 tc-med-2 tc-bold-last">
      <thead>
        <tr class="hidden">
        </tr>
      </thead>
      <tbody>
<?php if ($model->ondemand) : ?>
          <tr>
            <td class="<?= $model->ondemand->state==1 ? "text-success" : "text-danger"?>"><b><i class="fas fa-power-off"></i> OnDemand</b></td>
            <td><?= $model->ondemand->state==1 ? $model->ondemand->player->username : "" ?></td>
          </tr>
<?php endif; ?>
        <tr>
          <td><i class="fas fa-battery-half"></i> Difficulty</td>
          <td><?= $model->difficultyString ?></td>
        </tr>
        <tr>
          <td><i class="fas fa-tag"></i> Category</td>
          <td><?= $model->category ?></td>
        </tr>
        <tr>
          <td><i class="fas fa-baby"></i> Suggested XP</td>
          <td><?= $model->suggested_xp ?></td>
        </tr>
        <tr>
          <td><i class="fab fa-pied-piper-alt"></i> Required XP</td>
          <td><?= $model->required_xp ?></td>
        </tr>
        <tr>
          <td><i class="fas fa-file-medical-alt"></i> Status</td>
          <td><?= $model->status ?></td>
        </tr>
        <tr>
          <td><i class="fas fa-calendar-alt"></i> Scheduled at</td>
          <td><?= $model->scheduled_at ?></td>
        </tr>
        <tr>
          <td><i class="fas fa-balance-scale"></i> Weight</td>
          <td><?= $model->weight ?></td>
        </tr>
        <tr>
          <td><i class="fas fa-calendar-plus"></i> Created at</td>
          <td><?= $model->created_at ?></td>
        </tr>
        <tr>
          <td><i class="far fa-calendar-check"></i> Last update</td>
          <td><?= $model->ts ?></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>