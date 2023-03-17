<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\modules\gameplay\models\Target;
use app\modules\gameplay\models\Finding;
use app\modules\gameplay\models\Treasure;
use app\modules\gameplay\models\Challenge;
use app\modules\gameplay\models\Question;
use app\modules\frontend\models\Player;
use app\modules\frontend\models\Team;
use app\modules\activity\models\Report;
use app\modules\activity\models\Notification;
use app\modules\activity\models\Stream;
use app\modules\activity\models\PlayerTreasure;
use app\modules\activity\models\PlayerTargetHelp;
use app\modules\activity\models\PlayerFinding;
use app\modules\activity\models\PlayerQuestion;
use app\modules\activity\models\Writeup;
use app\modules\activity\models\PlayerScore;
use app\widgets\statscard\StatsCardModel;

$lastHeadshot=\app\modules\activity\models\Headshot::find()->orderBy(['created_at'=>SORT_DESC])->one();

?>
<div class="site-index">
  <h2 class="text-center">echoCTF Management interface</h1>
    <?php if (Yii::$app->user->isGuest) : ?>
      <div class="mt-4 p-5 rounded text-center">
        <p><img class="rounded" src="/images/logo.png" width="40%" /></p>
      </div>
    <?php endif; ?>

    <div class="body-content">
      <?php if (!Yii::$app->user->isGuest) : ?>
        <div class="row">
          <div class="col-xl-3 col-lg-6">
            <?= StatsCardModel::widget([
              'icon' => 'fas fa-skull',
              'color' => 'danger',
              'modelClass'=>'app\modules\activity\models\Headshot',
              'title' => Html::a('Headshots', ['/activity/headshot/index'])
            ]) ?>
          </div>
          <div class="col-xl-3 col-lg-6">
            <?= StatsCardModel::widget([
              'icon' => 'fas fa-users',
              'color' => 'warning',
              'modelClass'=>'app\modules\frontend\models\Player',
              'field'=>'created',
              'total' => '<abbr title="Total">' . number_format(intval(Player::find()->count())) . '</abbr> <small class="text-muted"><abbr title="Active">' . number_format(Player::find()->where(['active' => 1])->count()) . '</abbr></small>',
              'title' => Html::a('Players', ['/frontend/player/index'])
            ]); ?>
          </div>
          <div class="col-xl-3 col-lg-6">
            <?= StatsCardModel::widget([
              'icon' => 'fas fa-clipboard-list',
              'color' => 'info',
              'modelClass'=>'app\modules\activity\models\Stream',
              'field'=>'ts',
              'title' => Html::a('Stream', ['/activity/stream/index'])
            ]); ?>
          </div>
          <div class="col-xl-3 col-lg-6">
            <?= StatsCardModel::widget([
              'icon' => 'fas fa-question-circle',
              'color' => 'success',
              'modelClass'=>'app\modules\activity\models\PlayerTargetHelp',
              'title' => Html::a('Activated Help', ['/activity/player-target-help/index'])
            ]); ?>
          </div>

        </div>
        <br />
        <div class="row">
          <div class="col-lg-3">
            <h2>System</h2>
            <p>
            <ul>
              <li><?= Html::a('Players &raquo;', ['/frontend/player']) ?>: <abbr title="Total players"><?= Player::find()->count() ?></abbr> / <abbr title="Active players"><?= Player::find()->where(['active' => 1])->count() ?></abbr> / <abbr title="Online"><?= Player::find()->having(['>', 'online', 1])->count() ?></abbr> / <abbr title="On VPN"><?= Player::find()->having(['>', 'ovpn', 0])->count() ?></abbr>
              <li><?= Html::a('Teams &raquo;', ['/frontend/team']) ?>: <?= Team::find()->count() ?>
              <li><?= Html::a('Targets &raquo;', ['/infrastructure/target']) ?>: <?= Target::find()->count() ?> / <?= Target::find()->where(['active' => 1])->count() ?>
              <li><?= Html::a('Challenges &raquo;', ['/gameplay/challenge']) ?>: <?= Challenge::find()->count() ?>
              <li><?= Html::a('Questions &raquo;', ['/gameplay/question']) ?>: <?= Question::find()->count() ?> / <?= (int) (new \yii\db\Query())->from('question')->sum('points'); ?>pts
              <li><?= Html::a('Findings &raquo;', ['/gameplay/finding']) ?>: <?= Finding::find()->count() ?> / <?= (int) (new \yii\db\Query())->from('finding')->sum('points'); ?>pts
              <li><?= Html::a('Treasures &raquo;', ['/gameplay/treasure']) ?>: <?= Treasure::find()->count() ?> / <?= (int) (new \yii\db\Query())->from('treasure')->sum('points'); ?>pts
            </ul>
            </p>
          </div>
          <div class="col">
            <h2>Activity</h2>
            <p>
            <ul>
              <li><?= Html::a('Player Score &raquo;', ['/activity/player-score']) ?>: <abbr title="Players with non zero scores"><?= PlayerScore::find()->where(['>', 'points', 0])->count() ?></abbr> / <abbr title="Players with zero scores"><?= PlayerScore::find()->where(['points' => 0])->count() ?></abbr>
              <li><?= Html::a('Activated Help &raquo;', ['/activity/player-target-help']) ?>: <abbr title="Total activated player target writeups"><?= PlayerTargetHelp::find()->count() ?></abbr>
                / <abbr title="Contributed Writeups"><?= Writeup::find()->count() ?></abbr> / <abbr title="Pending Writeups"><?= Writeup::find()->byStatus('pending')->count(); ?>
              <li><?= Html::a('Player Treasures &raquo;', ['/activity/player-treasure']) ?>: <abbr title="Total player treasure records"><?= PlayerTreasure::find()->count() ?></abbr> / <abbr title="Distinct players on player treasure"><?= (new \yii\db\Query())->from('player_treasure')->select('player_id')->distinct()->count() ?></abbr> / <abbr title="Distinct treasure on player treasure"><?= (new \yii\db\Query())->from('player_treasure')->select('treasure_id')->distinct()->count() ?></abbr>
              <li><?= Html::a('Player Findings &raquo;', ['/activity/player-finding']) ?>: <abbr title="Total player finding records"><?= PlayerFinding::find()->count() ?></abbr> / <abbr title="Distinct players on player finding"><?= (new \yii\db\Query())->from('player_finding')->select('player_id')->distinct()->count() ?></abbr> / <abbr title="Distinct finding on player finding"><?= (new \yii\db\Query())->from('player_finding')->select('finding_id')->distinct()->count() ?></abbr>
              <li><?= Html::a('Player Questions &raquo;', ['/activity/player-question']) ?>: <abbr title="Total player question records"><?= PlayerQuestion::find()->count() ?></abbr> / <abbr title="Distinct players on player question"><?= (new \yii\db\Query())->from('player_question')->select('player_id')->distinct()->count() ?></abbr> / <abbr title="Distinct question on player question"><?= (new \yii\db\Query())->from('player_question')->select('question_id')->distinct()->count() ?></abbr>
              <li><?= Html::a('Notifications &raquo;', ['/activity/notification']) ?>: <abbr title="Total Notifications"><?= Notification::find()->count() ?></abbr> / <abbr title="Pending Notifications"><?= Notification::find()->where("[[archived]]=0")->count() ?></abbr>
            </ul>
            </p>
          </div>
          <div class="col">
            <h2>Infrastructure</h2>
            <p>
            <ul>
              <li><?= Html::a('Instances &raquo;', ['/infrastructure/target-instance/index']) ?>: <?=\app\modules\infrastructure\models\TargetInstance::find()->count()?>
              <li><?= Html::a('On Demand &raquo;', ['/infrastructure/target-ondemand/index']) ?>: <abbr title="Total targets on demand"?><?=\app\modules\gameplay\models\TargetOndemand::find()->count()?></abbr> / <abbr title="Powered on"><?=\app\modules\gameplay\models\TargetOndemand::find()->powered()->count()?></abbr>
              <li><?= Html::a('Schedules &raquo;', ['/infrastructure/network-target-schedule/index']) ?>: <?=\app\modules\infrastructure\models\NetworkTargetSchedule::find()->count()?>
            </ul>
            </p>
          </div>
          <div class="col">
            <h2>Last entries</h2>
            <p>
              <?= Html::ul(ArrayHelper::map(Report::find()->where(['status' => 'pending'])->all(), 'id', function ($model) {
                return Html::a($model['title'], ['activity/report/view', 'id' => $model['id']]);
              }), ['encode' => false]) ?>
            </p>
            <p>
              <?php if (Player::find()->count() > 0) : ?>
                <?php
                $lastPlayer=Player::find()->limit(1)->orderBy('id desc')->one();
                echo Html::a(sprintf('Player %d: <abbr title="%s" class="text-%s">%s</abbr> <sub>%s</sub>', $lastPlayer->id, Html::encode($lastPlayer->email),$lastPlayer->active ? 'success' : "danger",Html::encode($lastPlayer->username),Yii::$app->formatter->asRelativeTime($lastPlayer->created)), ['/frontend/profile/view-full', 'id' => Player::find()->limit(1)->orderBy('id desc')->one()->profile->id]) ?><br />
              <?php endif; ?>
              <?php if ($lastHeadshot):?>
              <?= Html::a('Headshot &raquo;', ['/activity/headshot/view','player_id'=>$lastHeadshot->player_id,'target_id'=>$lastHeadshot->target_id]) ?>: <?=sprintf("%s on %s <sub>%s</sub>",Html::a($lastHeadshot->player->username, ['/frontend/profile/view-full', 'id' => $lastHeadshot->player->profile->id]), Html::encode($lastHeadshot->target->name),\Yii::$app->formatter->asRelativeTime($lastHeadshot->created_at))?><br/>
              <?php endif;?>
              <?php if (Stream::find()->count() > 0) : ?>
                <?php $smsg = Stream::find()->select('stream.*,TS_AGO(stream.ts) as ts_ago')->limit(1)->orderBy('id desc')->one(); ?>
                <?= Html::a(sprintf("Stream %d: %s %s", $smsg->id, $smsg->formatted, $smsg->ts_ago), ['/activity/stream/view', 'id' => $smsg->id]) ?>
              <?php endif; ?>
            </p>

          </div>
        </div>
      <?php endif; ?>
    </div>
</div>