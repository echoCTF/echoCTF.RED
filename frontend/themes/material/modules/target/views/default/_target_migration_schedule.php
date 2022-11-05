<?php if($scheduled):?>
  <small class="text-danger scheduled_migration-text">
  <?php if ($scheduled->network):?>
    <?=\Yii::t('app','Target will be migrated to the {network_name}, on the {migration_date}.',['network_name'=>$scheduled->network->name,'migration_date'=>$scheduled->migration_date])?>
  <?php else:?>
    <?=\Yii::t('app','Target will be migrated out of {network_name}, on {migration_date}',['network_name'=>$network->name,'migration_date'=>$scheduled->migration_date])?>
  <?php endif;?>
  </small>
<?php endif;?>