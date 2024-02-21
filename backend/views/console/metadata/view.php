# Metadata for <?=$metadata->target->name?> (ID: <?=$metadata->target_id?>)

<?php if($metadata->scenario):?>
## Scenario
<?=$metadata->scenario."\n"?>
<?php endif;?>
<?php if($metadata->solution):?>
## Solution
<?=$metadata->solution."\n"?>
<?php endif;?>
<?php if($metadata->pre_credits):?>
## Pre Credits
<?=$metadata->pre_credits."\n"?>
<?php endif;?>
<?php if($metadata->post_credits):?>
## Post Credits
<?=$metadata->post_credits."\n"?>
<?php endif;?>
<?php if($metadata->pre_exploitation):?>
## Pre Exploitation
<?=$metadata->pre_exploitation."\n"?>
<?php endif;?>
<?php if($metadata->post_exploitation):?>
## Post Exploitation
<?=$metadata->post_exploitation."\n"?>
<?php endif;?>

