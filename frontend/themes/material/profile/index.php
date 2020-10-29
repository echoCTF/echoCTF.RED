<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use app\components\JustGage;
use app\widgets\stream\StreamWidget as Stream;
use app\widgets\target\TargetWidget;
use yii\widgets\Pjax;
use app\widgets\leaderboard\Leaderboard;
use app\modules\target\models\PlayerTargetHelp as PTH;
use app\modules\target\models\Writeup;
use yii\data\ActiveDataProvider;
$query = Writeup::find()->where(['player_id'=>$profile->player_id]);
$provider = new ActiveDataProvider([
    'query' => $query,
    'pagination' => [
        'pageSize' => 10,
    ],
]);
$game=Yii::$app->getModule('game');
$this->_fluid="-fluid";
$this->title=Yii::$app->sys->event_name.' Profile of: '.Html::encode($profile->owner->username);
$this->_description=Html::encode($profile->bio);
$this->_url=\yii\helpers\Url::to(['index', 'id'=>$profile->id], 'https');
?>
<div class="profile-index">
  <div class="body-content">
    <div class="row">
      <div class="col">
        <?php \yii\widgets\Pjax::begin(['id'=>'target-listing', 'enablePushState'=>false, 'linkSelector'=>'#target-pager a', 'formSelector'=>false]);?>
        <?php echo TargetWidget::widget(['dataProvider' => null, 'player_id'=>$profile->player_id, 'profile'=>$profile, 'title'=>'Progress', 'category'=>'Pending progress of '.Html::encode($profile->owner->username).' on platform targets.', 'personal'=>true]);?>
        <?php \yii\widgets\Pjax::end()?>
      </div>
      <div class="col-md-4">
        <?=$this->render('_card', ['profile'=>$profile]);?>
      </div><!-- // end profile card col-md-4 -->
    </div>
    <?php if((int)Writeup::find()->where(['player_id'=>$profile->player_id])->count()>0):?>
      <?php /* echo $this->render('_writeups', ['profile'=>$profile,'provider'=>$provider]);*/?>
    <?php endif;?>

    <?php if(count($profile->owner->challengeSolvers)>0):?>
      <h3><code><?=count($profile->owner->challengeSolvers)?></code> Challenges solved</h2>
      <div class="row">
        <?php foreach($profile->owner->challengeSolvers as $cs):?>
          <div class="col col-sm-1 col-md-5 col-lg-3">
            <div class="iconic-card">
                  <?=$cs->challenge->icon?>
                  <p><b><?=Html::a(
                          $cs->challenge->name,
                            Url::to(['/challenge/default/view', 'id'=>$cs->challenge_id]),
                            [
                              'style'=>'float: bottom;',
                              'title' => 'View target vs player card',
                              'aria-label'=>'View target vs player card',
                              'data-pjax' => '0',
                            ]
                        );?></b></p>
                          <p><b><i class="fas fa-list-ul text-info"></i> <?=count($cs->challenge->questions)?> Questions / <?=$cs->challenge->difficulty?></b><br /><b><i class="far fa-calendar-alt text-warning"></i> <?=\Yii::$app->formatter->asDate($cs->created_at,'long')?></b><br/>
                          <i class="fas fa-stopwatch text-danger"></i> <?=\Yii::$app->formatter->asDuration($cs->timer)?></p>
            </div>
          </div>
        <?php endforeach;?>
      </div>
    <?php endif;?>

    <?php if($profile->headshotsCount>0):?><h3><code><?=$profile->headshotsCount?></code> Headshots / <small>Average time: <?php
      $hs=\app\modules\game\models\Headshot::find()->player_avg_time($profile->player_id)->one();
      if($hs && $hs->average > 0)
        echo number_format($hs->average / 60), " minutes";
    ?> <sub>(ordered by date)</small></sub></h3>
    <div class="row">
      <?php foreach($profile->owner->headshots as $headshot):?>
      <div class="col col-sm-1 col-md-5 col-lg-3">
        <div class="iconic-card">
          <img align="right" src="<?=$headshot->target->thumbnail?>"/>
          <p><b><?=Html::a(
                      $headshot->target->name.' / '.long2ip($headshot->target->ip) ,
                        Url::to(['/target/default/versus', 'id'=>$headshot->target_id, 'profile_id'=>$profile->id]),
                        [
                          'style'=>'float: bottom;',
                          'title' => 'View target vs player card',
                          'aria-label'=>'View target vs player card',
                          'data-pjax' => '0',
                        ]
                    );?></b></p>
          <p><b><i class="far fa-calendar-alt text-warning"></i> <?=\Yii::$app->formatter->asDate($headshot->created_at,'long')?></b><br/>
            <?php if($headshot->writeup):?><b><i class="fas fa-book text-secondary"></i> Writeup submitted<?=$headshot->writeup->approved? '': ' ('.$headshot->writeup->status.')'?></b><br/><?php endif;?>
          <i class="fas fa-stopwatch text-danger"></i> <?=\Yii::$app->formatter->asDuration($headshot->timer)?>
          </p>
        </div>
      </div>
      <?php endforeach;?>
    </div>
    <?php endif;?>

    <?php if($game->badges !== null && $game->badges->received_by($profile->player_id)->count() > 0):?><h3>Badges</h3>
    <div class="row game-badges">
      <?php foreach($game->badges->received_by($profile->player_id)->all() as $badge):?>
      <div class="col col-sm-12 col-md-4 col-lg-3">
        <div class="iconic-card">
          <center><?=$badge->pubname?></center>
          <h3><?=$badge->name?></h3>
          <?php if(!Yii::$app->user->isGuest && $profile->player_id===Yii::$app->user->id):?>
            <p><?=$badge->description?></p>
          <?php else:?>
            <p><?=$badge->pubdescription?></p>
          <?php endif;?>
        </div>
      </div>
      <?php endforeach;?>
    </div>
    <?php endif;?>

    <div class="row">
      <div class="col">
        <?php
        Pjax::begin(['id'=>'global-leaderboard-listing', 'enablePushState'=>false, 'linkSelector'=>'#leaderboard-pager a', 'formSelector'=>false]);
        echo Leaderboard::widget(['divID'=>"Leaderboard", 'player_id'=>$profile->player_id, 'pageSize'=>8, 'title'=>'Global Leaderboard', 'category'=>'Listing current player page on a global scale. <small>Updated every 10 minutes</small>']);
        Pjax::end();
        ?>
      </div>
      <div class="col">
        <?php
        Pjax::begin(['id'=>'country-leaderboard-listing', 'enablePushState'=>false, 'linkSelector'=>'#country-leaderboard-pager a', 'formSelector'=>false]);
        echo Leaderboard::widget(['divID'=>"CountryLeaderboard", 'country'=>$profile->country,'player_id'=>$profile->player_id, 'pageSize'=>8, 'title'=>'Leaderboard for '.$profile->fullCountry->name, 'category'=>'Listing current player page for '.$profile->fullCountry->name.'. <small>Updated every 10 minutes</small>','pagerID'=>'country-leaderboard-pager']);
        Pjax::end();
        ?>
      </div>
    </div>
    <div class="row">
      <div class="col"><?php
      \yii\widgets\Pjax::begin(['id'=>'stream-listing', 'enablePushState'=>false, 'linkSelector'=>'#stream-pager a', 'formSelector'=>false]);
      echo Stream::widget(['divID'=>'stream', 'dataProvider' => null, 'player_id'=>$profile->player_id, 'pagerID'=>'stream-pager','title'=>'User History','category'=>'User history on the platform']);
      \yii\widgets\Pjax::end();
      ?></div>
    </div><!--/row-->

  </div><!--//body-content-->
</div><!--//profile-index-->
