<?php
use app\widgets\BooleanTransform as BT;
?>
<div class="panel">
  <div class="panel-heading">
    <span class="panel-icon">
      <i class="fas fa-memory"></i>
    </span>
    <span class="panel-title"> Booleans</span>
  </div>
  <div class="panel-body pn">
    <table class="table mbn tc-icon-1 tc-med-2 tc-bold-last">
      <thead>
        <tr class="hidden">
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><i class="fas fa-toggle-on"></i> Active</td>
          <td><?= BT::toOnOff($model->active) ?></td>
        </tr>
        <tr>
          <td><i class="fas fa-stopwatch"></i> Timer</td>
          <td><?= BT::toOnOff($model->timer) ?></td>
        </tr>
        <tr>
          <td><i class="fas fa-hashtag"></i> Rootable</td>
          <td><?= BT::toOnOff($model->rootable) ?></td>
        </tr>
        <tr>
          <td><i class="fas fa-heartbeat"></i> Healthcheck</td>
          <td><?= BT::toOnOff($model->healthcheck) ?></td>
        </tr>
        <tr>
          <td><i class="fas fa-file-alt"></i> Writeup allowed</td>
          <td><?= BT::toOnOff($model->writeup_allowed) ?></td>
        </tr>
        <tr>
          <td><i class="fas fa-skull"></i> Headshot spin</td>
          <td><?= BT::toOnOff($model->headshot_spin) ?></td>
        </tr>
        <tr>
          <td><i class="fas fa-sync-alt"></i> Player spin</td>
          <td><?= BT::toOnOff($model->player_spin) ?></td>
        </tr>
        <tr>
          <td><i class="fas fa-server"></i> Instance Allowed</td>
          <td><?= BT::toOnOff($model->instance_allowed) ?></td>
        </tr>
        <tr>
          <td><i class="fas fa-fingerprint"></i> Require findings</td>
          <td><?= BT::toOnOff($model->require_findings) ?></td>
        </tr>
        </tbody>
    </table>
  </div>
</div>