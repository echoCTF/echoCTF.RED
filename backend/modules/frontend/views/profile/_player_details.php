<?php
use yii\helpers\Html;

?>
<div class="panel">
  <div class="panel-heading">
    <span class="panel-icon">
      <i class="fas fa-id-card"></i>
    </span>
    <span class="panel-title"> Player details (<?=Html::a("profile: ".$model->id,"//".Yii::$app->sys->offense_domain.'/profile/'.$model->id,['target'=>'_blank'])?>)</span>
  </div>
  <div class="panel-body pn">
    <table class="table mbn tc-icon-1 tc-med-2 tc-bold-last">
      <thead>
        <tr class="hidden">
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><span class="fas fa-globe text-warning"></span></td>
          <td>country</td>
          <td><?=Html::encode($model->country)?></td>
        </tr>

        <tr>
          <td><span class="fas fa-eye-slash text-warning"></span></td>
          <td>visibility</td>
          <td><?=$model->visibility?></td>
        </tr>


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
          <td>
            <?php if($model->owner->active==0):?>
              <a href="//<?=Yii::$app->sys->offense_domain?>/verify-email?token=<?=$model->owner->verification_token?>" target="_blank"><i class="fas fa-times text-danger"></i></a>
            <?php else: ?>
              <i class="fas fa-check text-success"></i>
            <?php endif;?>
          </td>
        </tr>

        <tr>
          <td><span class="fas fa-at text-warning"></span></td>
          <td>status</td>
          <td><?=$model->owner->status==10? '<i class="fas fa-toggle-on text-success"></i>' :'<i class="fas fa-toggle-off text-info"></i>'?></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
