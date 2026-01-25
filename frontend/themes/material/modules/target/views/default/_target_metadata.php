<?php if(!Yii::$app->user->isGuest && $metadata):?>
  <?php if(Yii::$app->user->identity->isAdmin):?>
    <?php if(!empty($metadata->scenario)):?><b><?=\Yii::t('app','Scenario')?></b>: <?=\Yii::$app->formatter->asMarkdown($metadata->scenario)?><br/><?php endif;?>
    <?php if(!empty($metadata->instructions)):?><b><?=\Yii::t('app','Instructions')?></b>: <?=\Yii::$app->formatter->asMarkdown($metadata->instructions)?><br/><?php endif;?>
    <?php if(!empty($metadata->solution)):?><b><?=\Yii::t('app','Solution')?></b>: <?=\Yii::$app->formatter->asMarkdown($metadata->solution)?><br/><?php endif;?>
  <?php endif;?>
  <?php if(!empty($metadata->pre_credits)):?><b><?=\Yii::t('app','Pre exploitation credits')?></b>: <?=\Yii::$app->formatter->asMarkdown($metadata->pre_credits)?><br/><?php endif;?>
  <?php if(!empty($metadata->pre_exploitation)):?><b><?=\Yii::t('app','Pre exploitation details')?></b>: <?=\Yii::$app->formatter->asMarkdown($metadata->pre_exploitation)?><br/><?php endif;?>
  <?php if((($identity->player_id===Yii::$app->user->id && $target->progress==100) || Yii::$app->user->identity->isAdmin) && !empty($metadata->post_exploitation)):?><b><?=\Yii::t('app','Post exploitation')?></b>: <?=\Yii::$app->formatter->asMarkdown($metadata->post_exploitation)?><br/><?php endif;?>
  <?php if((($identity->player_id===Yii::$app->user->id && $target->progress==100) || Yii::$app->user->identity->isAdmin) && !empty($metadata->post_credits)):?><b><?=\Yii::t('app','Post exploitation credits')?></b>: <?=\Yii::$app->formatter->asMarkdown($metadata->post_credits)?><br/><?php endif;?>
<?php endif;?>
