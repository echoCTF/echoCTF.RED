<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
//use yii\bootstrap5\Tabs;
use kartik\tabs\TabsX;
use yii\bootstrap5\Dropdown;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\Profile */

$js = <<<SCRIPT
$(function () {
    $("[data-toggle='tooltip']").tooltip();
});;
$(function () {
    $("[data-toggle='popover']").popover();
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);

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
              <i class="fas fa-tasks"></i> <a href="#" data-toggle="dropdown" class="dropdown-toggle">Quick actions <b class="caret"></b></a>

              <?php echo Dropdown::widget([
                'items' => [
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
                'label' => '<abbr title="Activity Stream"><i class="fas fa-file-contract"></i></abbr>',
                'content' => $this->render('_activity_tab', ['model' => $model]),
                'headerOptions' => ['style' => 'font-weight:bold'],
                'options' => ['id' => 'stream-tab'],
                'active' => true,
              ],
              [
                'label' => '<abbr title="Headshots"><i class="fas fa-skull-crossbones"></i></abbr>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['headshots', 'id' => $model->id])],
                'options' => ['id' => 'headshots-tab'],
              ],
              [
                'label' => '<abbr title="Submitted Writeups"><i class="fas fa-book-dead"></i></abbr>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['writeups', 'id' => $model->id])],
                'options' => ['id' => 'writeups-tab'],
              ],
              [
                'label' => '<abbr title="Activated Writeups"><i class="fas fa-notes-medical"></i></abbr>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['activated-writeups', 'id' => $model->id])],
                'options' => ['id' => 'activated-writeups-tab'],
              ],
              [
                'label' => '<abbr title="Challenge Solves"><i class="fas fa-tasks"></i></abbr>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['solves', 'id' => $model->id])],
                'options' => ['id' => 'solves-tab'],
              ],
              [
                'label' => '<abbr title="VPN History"><i class="fas fa-history"></i></abbr>',
                'headerOptions' => ['style' => 'font-weight:bold'],
                'linkOptions' => ['data-url' => Url::to(['vpn-history', 'id' => $model->id])],
                'options' => ['id' => 'vpn-history-tab'],
              ],
            ],
          ]);
          ?>
        </div>
      </div>
    </div>
  </div>
</section>