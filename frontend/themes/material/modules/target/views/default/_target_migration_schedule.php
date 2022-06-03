<?php if($scheduled):?>
  <small class="text-danger scheduled_migration-text">
  <?php if ($scheduled->network):?>
    Target will be migrated to the <?=$scheduled->network->name?>, on the <?=$scheduled->migration_date?>.
  <?php else:?>
    Target will be migrated out of <?=$network->name?>, on <?=$scheduled->migration_date?>
  <?php endif;?>
  </small>
<?php endif;?>