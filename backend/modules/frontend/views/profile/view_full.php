<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
use yii\bootstrap5\Dropdown;
use yii\bootstrap5\ButtonDropdown;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\Profile */

$this->title = "View Profile for " . Html::encode($model->owner->username) . " profile: " . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Profiles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<section class="container">
  <div class="profile-view-full">
    <!-- Begin .page-heading -->
    <p></p>
    <?= $this->render('_heading', ['model' => $model]); ?>
    <div class="row">
      <div class="col-md-4">
        <div class="panel">
          <div class="panel-heading">
            <div class="dropdown">
              <?php
              echo ButtonDropdown::widget([
                'label'=>'Quick actions',
                'options'=>['encodeLabels'=>false],
                'dropdown' => [
                  'items'=> [
                    ['label' => 'Download player ovpn', 'url' => ['/frontend/player/ovpn', 'id' => $model->player_id]],
                    ['label' => 'Update profile', 'url' => ['update', 'id' => $model->id]],
                    ['label' => 'Delete profile', 'url' => ['delete', 'id' => $model->id], 'linkOptions' => ['data' => [
                      'confirm' => Yii::t('app', 'Are you sure you want to delete this profile?'),
                      'method' => 'post',
                    ],]],
                    ['label' => 'View player', 'url' => ['player/view', 'id' => $model->player_id]],
                    ['label' => 'Update player', 'url' => ['player/update', 'id' => $model->player_id]],
                    ['label' => 'Delete player', 'url' => ['player/delete', 'id' => $model->player_id], 'linkOptions' => ['data' => [
                      'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                      'method' => 'post',
                    ],]],
                    ['label' => 'Player profile', 'url' => "//" . Yii::$app->sys->offense_domain . '/profile/' . $model->id, 'linkOptions' => ['target' => '_blank']],
                    ['label' => 'Reset auth_key', 'url' => ['player/reset-authkey', 'id' => $model->player_id], 'linkOptions' => [
                      'class' => 'text-danger',
                      'title' => 'Reset player auth_key (force logout)',
                      'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to reset the player auth_key?'),
                        'method' => 'post',
                      ],
                    ]],
                    ['label' => 'Activation URL', 'url' => "//" . Yii::$app->sys->offense_domain . '/verify-email?token=' . $model->owner->verification_token, 'linkOptions' => ['target' => '_blank'], 'visible' => $model->owner->verification_token != null],
                    ['label' => 'Password Reset URL', 'url' => "//" . Yii::$app->sys->offense_domain . '/reset-password?token=' . $model->owner->password_reset_token, 'linkOptions' => ['target' => '_blank'], 'visible' => $model->owner->password_reset_token != null],
                  ],
                ]
              ]);
              ?>
            </div>
          </div>
        </div>
        <?= $this->render('_player_details', ['model' => $model]); ?>
        <?= $this->render('_player_memcache_details', ['model' => $model]); ?>
        <?= $this->render('_player_counters_details', ['model' => $model]); ?>
        <?= $this->render('_player_date_details', ['model' => $model]); ?>
        <?= $this->render('_player_meta_details', ['model' => $model]); ?>
        <?= $this->render('_player_relations', ['model' => $model]); ?>
        <?= $this->render('_player_badges', ['model' => $model]); ?>
      </div>
      <div class="col-md-8">
        <div class="tab-block">
          <?php
          echo TabsX::widget([
            'position' => TabsX::POS_ABOVE,
            'align' => TabsX::ALIGN_LEFT,
            'encodeLabels'=>false,
            'pluginOptions' => [
              'enableCache' => false,
              'cacheTimeout' => 10000
            ],
            'items' => [
              [
                'label' => '<i class="fas fa-file-contract" data-toggle="tooltip" data-placement="top" title="Activity Stream"></i>',
                'content' => $this->render('_activity_tab', ['model' => $model]),
                'headerOptions' => ['style' => 'font-weight:bold'],
                'options' => ['id' => 'stream-tab'],
                'active' => true,
              ],
              [
                'label' => '<i class="fas fa-calendar" data-toggle="tooltip" data-placement="top" title="Monthly Scores"></i>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['score-monthly', 'id' => $model->id])],
                'options' => ['id' => 'score-monthly-tab'],
              ],
              [
                'label' => '<i class="fas fa-spinner" data-placement="top" title="Target Progress"></i>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['target-progress', 'id' => $model->id])],
                'options' => ['id' => 'target-progress-tab'],
              ],
              [
                'label' => '<i class="fas fa-skull-crossbones" data-toggle="tooltip" data-placement="top" title="Headshots"></i>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['headshots', 'id' => $model->id])],
                'options' => ['id' => 'headshots-tab'],
              ],
              [
                'label' => '<i class="fas fa-book-dead" data-toggle="tooltip" data-placement="top" title="Submitted Writeups"></i>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['writeups', 'id' => $model->id])],
                'options' => ['id' => 'writeups-tab'],
              ],
              [
                'label' => '<i class="fas fa-notes-medical" data-toggle="tooltip" data-placement="top" title="Activated Writeups"></i>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['activated-writeups', 'id' => $model->id])],
                'options' => ['id' => 'activated-writeups-tab'],
              ],
              [
                'label' => '<i class="fas fa-comment-medical" data-toggle="tooltip" data-placement="top" title="Rated Writeups"></i>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['writeup-rating', 'id' => $model->id])],
                'options' => ['id' => 'writeup-rating-tab'],
              ],
              [
                'label' => '<i class="fas fa-tasks" data-toggle="tooltip" data-placement="top" title="Challenge Solves"></i>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['solves', 'id' => $model->id])],
                'options' => ['id' => 'solves-tab'],
              ],
              [
                'label' => '<i class="fas fa-history" data-toggle="tooltip" data-placement="top" title="VPN History"></i>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['vpn-history', 'id' => $model->id])],
                'options' => ['id' => 'vpn-history-tab'],
              ],
              [
                'label' => '<i class="fas fa-sync" data-toggle="tooltip" data-placement="top" title="Spin History"></i>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['spin-history', 'id' => $model->id])],
                'options' => ['id' => 'spin-history-tab'],
              ],
              [
                'label' => '<i class="fas fa-exclamation-triangle" data-toggle="tooltip" data-placement="top" title="Notifications"></i>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['notifications', 'id' => $model->id])],
                'options' => ['id' => 'notifications-tab'],
              ],
            ],
          ]);
          ?>
        </div>
      </div>
    </div>
  </div>
</section>