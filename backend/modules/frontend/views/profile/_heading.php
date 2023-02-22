<?php
use yii\helpers\Html;
?>
<div class="page-heading">
    <div class="media clearfix">
      <div class="media-left pr30">
        <a href="//<?=Yii::$app->sys->offense_domain?>/profile/<?=$model->id?>" target="_blank">
          <img width="140px" class="img-fluid" src="//<?=Yii::$app->sys->offense_domain?>/images/avatars/<?=$model->avatar?>" alt="<?=Yii::$app->sys->offense_domain?>/images/avatars/<?=$model->avatar?>">
        </a>
      </div>
      <div class="media-body va-m">
        <h2 class="media-heading"><?=Html::encode($model->owner->username)?>
          <small> - <?=Html::encode($model->owner->fullname)?></small>
        </h2>
         <p>ranked <?=$model->owner->rank->ordinalPlace?> with <?=number_format($model->owner->score->points)?> points</p>
        <p class="lead"><?=Html::encode($model->bio)?></p>
        <div class="media-links">
          <ul class="list-inline list-unstyled breadcrumb">
            <?php if($model->twitter):?>
            <li>
              <a href="https://twitter.com/<?=Html::encode($model->twitter)?>" title="<?=Html::encode($model->twitter)?>" target="_blank">
                <span class="fab fa-twitter fs35 <?=$model->validate('twitter') ? "text-info" : "text-danger" ?>"></span>
              </a>
            </li>
            <?php endif;?>
            <?php if($model->github):?>
            <li>
              <a href="https://github.com/<?=Html::encode($model->github)?>" title="<?=Html::encode($model->github)?>">
                <span class="fab fa-github fs35 <?=$model->validate('github') ? "text-info" : "text-danger" ?>"></span>
              </a>
            </li>
            <?php endif;?>
            <?php if($model->htb):?>
            <li>
              <a href="https://hackthebox.eu/<?=Html::encode($model->htb)?>" title="<?=Html::encode($model->htb)?>">
                <span class="fab fa-codepen fs35 <?=$model->validate('htb') ? "text-info" : "text-danger" ?>"></span>
              </a>
            </li>
            <?php endif;?>
            <?php if($model->youtube):?>
            <li>
              <a href="https://youtube.com/<?=Html::encode($model->youtube)?>" title="<?=Html::encode($model->youtube)?>">
                <span class="fab fa-youtube fs35 <?=$model->validate('youtube') ? "text-info" : "text-danger" ?>"></span>
              </a>
            </li>
            <?php endif;?>
            <?php if($model->twitch):?>
            <li>
              <a href="https://twitch.tv/<?=Html::encode($model->twitch)?>" title="<?=Html::encode($model->twitch)?>">
                <span class="fab fa-twitch fs35 <?=$model->validate('twitch') ? "text-info" : "text-danger" ?>"></span>
              </a>
            </li>
            <?php endif;?>
            <?php if($model->discord):?>
            <li>
              <a href="https://discord.gg/<?=Html::encode($model->discord)?>" title="<?=Html::encode($model->discord)?>" target="_blank">
                <span class="fab fa-discord fs35 <?=$model->validate('discord') ? "text-info" : "text-danger" ?>"></span>
              </a>
            </li>
            <?php endif;?>
          </ul>
        </div>
      </div>
    </div>
</div>
