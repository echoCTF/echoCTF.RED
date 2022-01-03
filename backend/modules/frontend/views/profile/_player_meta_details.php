<?php
use yii\helpers\Html;

?>
<div class="panel">
  <div class="panel-heading">
    <span class="panel-icon">
      <i class="fas fa-network-wired"></i>
    </span>
    <span class="panel-title"> Meta details</span>
  </div>
  <div class="panel-body pn">
    <table class="table mbn tc-icon-1 tc-med-2 tc-bold-last">
      <thead>
        <tr class="hidden">
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><i class="fas fa-server text-warning"></td>
          <td>Remote IP</td>
          <td><?=Html::encode(long2ip($model->owner->last->vpn_remote_address))?></td>
        </tr>
        <tr>
          <td><i class="fas fa-network-wired text-info"></i></i></span></td>
          <td>Local IP</td>
          <td><?=Html::encode(long2ip($model->owner->last->vpn_local_address))?></td>
        </tr>

        <tr>
          <td><i class="far fa-registered text-danger"></i></span></td>
          <td>Registration IP</td>
          <td><?=Html::encode($model->owner->last->signup_ipoctet)?></td>
        </tr>
        <tr>
          <td><i class="fab fa-first-order text-danger"></i></td>
          <td>1st Login IP</td>
          <td><?=Html::encode($model->owner->last->signin_ipoctet)?></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
