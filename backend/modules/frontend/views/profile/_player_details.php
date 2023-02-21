<?php
use yii\helpers\Html;

?>
<div class="panel">
  <div class="panel-heading">
    <span class="panel-icon">
      <i class="fas fa-id-card"></i>
    </span>
    <span class="panel-title"><small><?=$model->player_id?></small> Player details (<?=Html::a("profile: ".$model->id,"//".Yii::$app->sys->offense_domain.'/profile/'.$model->id,['target'=>'_blank'])?>)  <?= Html::a('<i class="fas fa-sign-out-alt"></i>', ['/frontend/player/reset-authkey', 'id' => $model->player_id], [
          'class' => 'text-danger',
          'title'=>'Reset player auth_key (force logout)',
          'data-toggle'=>'tooltip',
          'data' => [
              'rel'=>'tooltip',
              'confirm' => Yii::t('app', 'Are you sure you want to reset the player auth_key?'),
              'method' => 'post',
          ],
      ]) ?></span>
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
          <td><span class="fas fa-tasks text-warning"></span></td>
          <td>pending progress</td>
          <td><?=\Yii::$app->formatter->asBoolean($model->pending_progress)?></td>
        </tr>

        <tr>
          <td><span class="fas fa-at text-warning"></span></td>
          <td>email</td>
          <td><?=Html::encode($model->owner->email)?></td>
        </tr>

        <tr>
          <td><span class="fas fa-user-graduate text-warning"></span></td>
          <td>academic</td>
          <td><?= Html::a($model->owner->academic==0? '<i class="fas fa-times text-danger"></i>' :'<i class="fas fa-check text-success"></i>', ['/frontend/player/toggle-academic', 'id' => $model->player_id], [
                'class' => 'text-danger',
                'title'=>'Toggle player academic status',
                'data-toggle'=>'tooltip',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to toggle the player academic key?'),
                    'method' => 'post',
                ],
            ]) ?></td>
        </tr>

        <tr>
          <td><i class="fas fa-address-card text-warning"></i></td>
          <td>active</td>
          <td>
            <?php if($model->owner->active==0):?>
              <a href="//<?=Yii::$app->sys->offense_domain?>/verify-email?token=<?=$model->owner->verification_token?>" target="_blank"><i class="fas fa-times text-danger"></i></a>
            <?php else: ?>
              <?= Html::a('<i class="fas fa-check text-success"></i>', ['/frontend/player/toggle-active', 'id' => $model->player_id], [
                    'class' => 'text-danger',
                    'title'=>'Toggle player active status',
                    'data-toggle'=>'tooltip',
                    'data' => [
                        'rel'=>'tooltip',
                        'confirm' => Yii::t('app', 'Are you sure you want to toggle the player active key?'),
                        'method' => 'post',
                    ],
                ]) ?>

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
