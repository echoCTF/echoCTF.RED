<?php
use yii\helpers\Html;

?>
<div class="panel">
  <div class="panel-heading">
    <span class="panel-icon">
      <i class="fas fa-calendar"></i>
    </span>
    <span class="panel-title"> Dates</span>
  </div>
  <div class="panel-body pn">
    <table class="table mbn tc-icon-1 tc-med-2 tc-bold-last">
      <thead>
        <tr class="hidden">
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><i class="fas fa-calendar-day text-warning"></i></span></td>
          <td>registration</td>
          <td><?=Html::encode($model->owner->created)?></td>
        </tr>
        <tr>
          <td><i class="fas fa-calendar-day text-warning"></i></span></td>
          <td>profile update</td>
          <td><?=Html::encode($model->updated_at)?></td>
        </tr>

        <tr>
          <td><i class="far fa-user text-success"></i></td>
          <td>on frontend</td>
          <td><?=Html::encode($model->owner->last->on_pui)?></td>
        </tr>
        <tr>
          <td><i class="fas fa-user-shield text-success"></i></td>
          <td>on vpn</td>
          <td><?=Html::encode($model->owner->last->on_vpn)?></td>
        </tr>
        <tr>
          <td><i class="fas fa-calendar-alt"></i></td>
          <td>last spin</td>
          <td><?=Html::encode($model->owner->playerSpin->updated_at)?></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
