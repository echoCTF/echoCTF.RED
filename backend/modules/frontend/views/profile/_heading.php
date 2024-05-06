<?php

use yii\helpers\Html;
?>
<div class="page-heading">
  <div class="media clearfix">
    <div class="media-left pr30">
      <a href="//<?= Yii::$app->sys->offense_domain ?>/profile/<?= $model->id ?>" target="_blank">
        <img width="140px" class="img-fluid img-thumbnail" src="//<?= Yii::$app->sys->offense_domain ?>/images/avatars/<?= $model->avatar ?>" alt="<?= Yii::$app->sys->offense_domain ?>/images/avatars/<?= $model->avatar ?>">
      </a>
      <?php if ($model->owner->active):?>
      <img align="right" class="img-fluid" src="//<?= Yii::$app->sys->offense_domain ?>/images/avatars/badges/<?= $model->id ?>.png" alt="Profile badge">
      <?php endif;?>
    </div>
    <div class="media-body va-m">
      <h2 class="media-heading"><?= Html::encode($model->owner->username) ?>
        <small><?php if(trim($model->owner->fullname)!=="") echo " - ",Html::encode($model->owner->fullname) ?><?php if($model->owner->metadata && trim($model->owner->metadata->affiliation)!=="") echo " - ",Html::encode($model->owner->metadata->affiliation) ?></small>
      </h2>
      <?php if($model->owner->teamPlayer):?><p>Team: <?= $model->owner->teamPlayer->team->name ?></p><?php endif;?>
      <p class="lead"><?= Html::encode($model->bio) ?></p>
      <div class="media-links">
        <ul class="list-inline list-unstyled breadcrumb">
          <?php if ($model->owner->stripe_customer_id) : ?>
            <li>
              <?= Html::a('<span class="fab fa-stripe fs-2 text-info"></span>', "https://dashboard.stripe.com/customers/" . $model->owner->stripe_customer_id, ['target' => '_blank', 'title' => 'Go to stripe customer', 'data-toggle' => "tooltip", 'data-placement' => "top"]); ?>
            </li>
          <?php endif; ?>
          <?php if($model->echoctf):?>
            <li>
              <a href="https://echoctf.red/profile/<?=Html::encode($model->echoctf)?>"  data-toggle="tooltip" data-placement="top" title="echoCTF profile <?=Html::encode($model->echoctf)?>" target="_blank">
                <span class="fas fa-flag fs-2 <?=$model->validate('echoctf') ? "text-info" : "text-danger" ?>"></span>
              </a>
            </li>
          <?php endif;?>
          <?php if ($model->twitter) : ?>
            <li>
              <a href="https://twitter.com/<?= Html::encode($model->twitter) ?>" data-toggle="tooltip" data-placement="top" title="<?= Html::encode($model->twitter) ?>" target="_blank">
                <span class="fab fa-twitter fs-2 <?= $model->validate('twitter') ? "text-info" : "text-danger" ?>"></span>
              </a>
            </li>
          <?php endif; ?>
          <?php if ($model->github) : ?>
            <li>
              <a href="https://github.com/<?= Html::encode($model->github) ?>" data-toggle="tooltip" data-placement="top" title="<?= Html::encode($model->github) ?>">
                <span class="fab fa-github fs-2 <?= $model->validate('github') ? "text-info" : "text-danger" ?>"></span>
              </a>
            </li>
          <?php endif; ?>
          <?php if ($model->htb) : ?>
            <li>
              <a href="https://hackthebox.eu/<?= Html::encode($model->htb) ?>" data-toggle="tooltip" data-placement="top" title="<?= Html::encode($model->htb) ?>">
                <span class="fab fa-codepen fs-2 <?= $model->validate('htb') ? "text-info" : "text-danger" ?>"></span>
              </a>
            </li>
          <?php endif; ?>
          <?php if ($model->youtube) : ?>
            <li>
              <a href="https://youtube.com/<?= Html::encode($model->youtube) ?>" data-toggle="tooltip" data-placement="top" title="<?= Html::encode($model->youtube) ?>">
                <span class="fab fa-youtube fs-2 <?= $model->validate('youtube') ? "text-info" : "text-danger" ?>"></span>
              </a>
            </li>
          <?php endif; ?>
          <?php if ($model->twitch) : ?>
            <li>
              <a href="https://twitch.tv/<?= Html::encode($model->twitch) ?>" data-toggle="tooltip" data-placement="top" title="<?= Html::encode($model->twitch) ?>">
                <span class="fab fa-twitch fs-2 <?= $model->validate('twitch') ? "text-info" : "text-danger" ?>"></span>
              </a>
            </li>
          <?php endif; ?>
          <?php if ($model->discord) : ?>
            <li>
              <a href="https://discord.gg/<?= Html::encode($model->discord) ?>" data-toggle="tooltip" data-placement="top" title="<?= Html::encode($model->discord) ?>" target="_blank">
                <span class="fab fa-discord fs-2 <?= $model->validate('discord') ? "text-info" : "text-danger" ?>"></span>
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </div>
</div>