<?php
use yii\helpers\Html;

?>
<div class="panel">
  <div class="panel-heading">
    <span class="panel-icon">
      <i class="fas fa-id-card"></i>
    </span>
    <span class="panel-title"> Player details</span>
  </div>
  <div class="panel-body pn">
    <table class="table mbn tc-icon-1 tc-med-2 tc-bold-last">
      <thead>
        <tr class="hidden">
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><span class="fas fa-at text-warning"></span></td>
          <td>email</td>
          <td><?=Html::encode($model->owner->email)?></td>
        </tr>

        <tr>
          <td><span class="fas fa-user-graduate text-warning"></span></td>
          <td>academic</td>
          <td><?=$model->owner->academic==0? '<i class="fas fa-times text-danger"></i>' :'<i class="fas fa-check text-success"></i>'?></td>
        </tr>

        <tr>
          <td><i class="fas fa-address-card text-warning"></i></td>
          <td>active</td>
          <td><?=$model->owner->active==0? '<i class="fas fa-times text-danger"></i>' :'<i class="fas fa-check text-success"></i>'?></td>
        </tr>

        <tr>
          <td><span class="fas fa-at text-warning"></span></td>
          <td>status</td>
          <td><?=$model->owner->status==10? '<i class="fas fa-toggle-on text-success"></i>' :'<i class="fas fa-toggle-off text-info"></i>'?></td>
        </tr>

        <tr>
          <td><i class="fas fa-calendar-day text-warning"></i></span></td>
          <td>registration</td>
          <td><?=Html::encode($model->owner->created)?></td>
        </tr>

      </tbody>
    </table>
  </div>
</div>
