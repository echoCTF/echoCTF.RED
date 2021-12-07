<?php
use yii\helpers\Html;

?>
<div class="panel">
  <div class="panel-heading">
    <span class="panel-icon">
      <i class="far fa-address-card"></i>
    </span>
    <span class="panel-title"> Profile details (<?=Html::a("ID: ".$model->id,"//".Yii::$app->sys->offense_domain.'/profile/'.$model->id,['target'=>'_blank'])?>)</span>
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
          <td>country</td>
          <td><?=Html::encode($model->country)?></td>
        </tr>

        <tr>
          <td><span class="fas fa-eye-slash text-warning"></span></td>
          <td>visibility</td>
          <td><?=$model->visibility?></td>
        </tr>

        <tr>
          <td><i class="fas fa-calendar-day text-warning"></i></span></td>
          <td>last update</td>
          <td><?=Html::encode($model->updated_at)?></td>
        </tr>

      </tbody>
    </table>
  </div>
</div>
