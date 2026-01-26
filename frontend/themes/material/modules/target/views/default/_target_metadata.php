<?php if(!Yii::$app->user->isGuest && $metadata):?>
  <?php $oldId=\Yii::$app->formatter->divID; ?>
  <?php if(Yii::$app->user->identity->isAdmin):?>
    <?php if(!empty($metadata->scenario)):?><b><?=\Yii::t('app','Scenario')?></b>: <?php \Yii::$app->formatter->divID = 'markdown-scenario'; echo \Yii::$app->formatter->asMarkdown($metadata->scenario)?><?php endif;?>
    <?php if(!empty($metadata->instructions)):?><b><?=\Yii::t('app','Instructions')?></b>: <?php \Yii::$app->formatter->divID = 'markdown-instructions'; echo \Yii::$app->formatter->asMarkdown($metadata->instructions)?><?php endif;?>
    <?php if(!empty($metadata->solution)):?><b><?=\Yii::t('app','Solution')?></b>: <?php \Yii::$app->formatter->divID = 'markdown-solution'; echo \Yii::$app->formatter->asMarkdown($metadata->solution)?><?php endif;?>
  <?php endif;?>
  <?php if(!empty($metadata->pre_credits)):?><b><?=\Yii::t('app','Pre exploitation credits')?></b>: <?php \Yii::$app->formatter->divID = 'markdown-pre-credits'; echo \Yii::$app->formatter->asMarkdown($metadata->pre_credits)?><?php endif;?>
  <?php if(!empty($metadata->pre_exploitation)):?><b><?=\Yii::t('app','Pre exploitation details')?></b>: <?php \Yii::$app->formatter->divID = 'markdown-pre-exploitation'; echo \Yii::$app->formatter->asMarkdown($metadata->pre_exploitation)?><?php endif;?>
  <?php if((($identity->player_id===Yii::$app->user->id && $target->progress==100) || Yii::$app->user->identity->isAdmin) && !empty($metadata->post_exploitation)):?><b><?=\Yii::t('app','Post exploitation')?></b>: <?php \Yii::$app->formatter->divID = 'markdown-post-exploitation'; echo \Yii::$app->formatter->asMarkdown($metadata->post_exploitation)?><?php endif;?>
  <?php if((($identity->player_id===Yii::$app->user->id && $target->progress==100) || Yii::$app->user->identity->isAdmin) && !empty($metadata->post_credits)):?><b><?=\Yii::t('app','Post exploitation credits')?></b>: <?php \Yii::$app->formatter->divID = 'markdown-post-credits'; echo \Yii::$app->formatter->asMarkdown($metadata->post_credits)?><?php endif;?>
  <?php \Yii::$app->formatter->divID=$oldId; ?>
<?php endif;?>
