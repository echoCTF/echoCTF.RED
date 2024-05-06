<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;

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
            <?= $this->render('_quick_actions',['model'=>$model]); ?>
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
            'encodeLabels' => false,
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