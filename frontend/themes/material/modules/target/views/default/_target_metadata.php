<?php if(!Yii::$app->user->isGuest && $target->metadata):?>
  <?php if(Yii::$app->user->identity->isAdmin && !empty($target->metadata->scenario)):?><b>Scenario</b>: <?=$target->metadata->scenario?><br/><?php endif;?>
  <?php if(Yii::$app->user->identity->isAdmin && !empty($target->metadata->instructions)):?><b>Instructions</b>: <?=$target->metadata->instructions?><br/><?php endif;?>
  <?php if(Yii::$app->user->identity->isAdmin && !empty($target->metadata->solution)):?><b>Solution</b>: <?=$target->metadata->solution?><br/><?php endif;?>
  <?php if(!empty($target->metadata->pre_credits)):?><b>Pre exploitation Credits</b>: <?=$target->metadata->pre_credits?><br/><?php endif;?>
  <?php if(!empty($target->metadata->pre_exploitation)):?><b>Pre exploitation Details</b>: <?=$target->metadata->pre_exploitation?><br/><?php endif;?>
  <?php if((($identity->player_id===Yii::$app->user->id && $target->progress==100) || Yii::$app->user->identity->isAdmin) && !empty($target->metadata->post_exploitation)):?><b>Post exploitation</b>: <?=$target->metadata->post_exploitation?><br/><?php endif;?>
  <?php if((($identity->player_id===Yii::$app->user->id && $target->progress==100) || Yii::$app->user->identity->isAdmin) && !empty($target->metadata->post_exploitation)):?><b>Post exploitation credits</b>: <?=$target->metadata->post_credits?><br/><?php endif;?>
<?php endif;?>
