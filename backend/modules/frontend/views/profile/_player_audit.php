<?php
use yii\helpers\Html;
use app\modules\frontend\models\PlayerAudit;

$changes = PlayerAudit::find()->where(['player_id' => $model->player_id])->orderBy(['id' => SORT_DESC])->limit(10)->all();
?>
<?php if ($changes !== []): ?>
<div class="panel">
  <div class="panel-heading">
    <span class="panel-icon">
      <i class="fas fa-history"></i>
    </span>
    <span class="panel-title"> Recent changes (<?=Html::a('all', ['/frontend/player-audit/index', 'PlayerAuditSearch[player_id]' => $model->player_id])?>)</span>
  </div>
  <div class="panel-body pn">
    <table class="table mbn">
      <tbody>
        <?php foreach ($changes as $change): ?>
        <tr>
          <td><small><?=Html::encode($change->ts)?></small></td>
          <td><?=Html::encode($change->field)?></td>
          <td><?=Html::encode($change->old_value)?> <i class="fas fa-arrow-right text-warning"></i> <?=Html::encode($change->new_value)?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>
