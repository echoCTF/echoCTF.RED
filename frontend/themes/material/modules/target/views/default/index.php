<?php
use yii\widgets\Pjax;
use app\widgets\Card;
use app\widgets\target\TargetWidget;
use app\widgets\leaderboard\Leaderboard;
use app\widgets\stream\StreamWidget as Stream;
$this->_fluid="-fluid";
$this->title=Yii::$app->sys->event_name.' '.\Yii::t('app','Targets');
if(Yii::$app->request->get('_pjax')) $this->title=null;
$this->_description=\Yii::t('app',"The echoCTF dashboard page");
$hidden_attributes=['id'];
?>

<div class="target-index">
  <div class="body-content">
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="fa fa-flag"></i>',
                'color'=>'primary',
                'title'=>$pageStats->totalTreasures,
                'subtitle'=>\Yii::t('app','Flags'),
                'footer'=>'<div class="stats">
                        <i class="material-icons text-danger">flag</i>'.$pageStats->ownClaims.' '.\Yii::t('app','claimed by you').'
                      </div>',
            ]);Card::end();?>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="fas fa-fingerprint"></i>',
                'color'=>'warning',
                'title'=>$pageStats->totalFindings,
                'subtitle'=>\Yii::t('app','Services'),
                'footer'=>'<div class="stats">
                        <i class="material-icons text-danger">track_changes</i> '.$pageStats->ownFinds.' '.\Yii::t('app','services found by you').'
                      </div>',
            ]);Card::end();?>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="fa fa-skull"></i>',
                'color'=>'danger',
                'title'=>$pageStats->totalHeadshots,
                'subtitle'=>\Yii::t('app','Headshots'),
                'footer'=>'<div class="stats">
                        <i class="material-icons text-danger">memory</i> '.$pageStats->ownHeadshots.' '.\Yii::t('app','headshots by you').'
                      </div>',
            ]);Card::end();?>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="fas fa-medal"></i>',
                'color'=>'info',
                'title'=>number_format($pageStats->totalPoints),
                'subtitle'=>\Yii::t('app','Points'),
                'footer'=>'<div class="stats">
                        <i class="material-icons text-danger">format_list_numbered</i> '.number_format(Yii::$app->user->identity->playerScore->points).' '.\Yii::t('app','yours').'
                      </div>',
            ]);Card::end();?>
        </div>

    </div>
    <div class="row justify-content-center">
      <div class="col">
      <?php Pjax::begin(['id'=>'target-listing-pjax', 'enablePushState'=>false, 'linkSelector'=>'#target-pager a', 'formSelector'=>false]);?>
      <?php echo TargetWidget::widget(['dataProvider' => null, 'hidden_attributes'=>$hidden_attributes,'player_id'=>Yii::$app->user->id,'pageSize'=>10,/*'buttonsTemplate'=>null*/]);?>
      <?php Pjax::end();?>
      </div>
    </div><!-- //row -->
  </div><!-- //body-content -->
</div>
