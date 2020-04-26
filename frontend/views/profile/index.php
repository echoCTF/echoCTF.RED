<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = Yii::$app->sys->event_name .' - Profile of '.Html::encode($profile->owner->username);
//$this->pageDescription=Html::encode(str_replace("\n","",strip_tags($profile->bio)));
//$this->pageImage=Yii::$app->getBaseUrl(true)."/images/avatars/".$profile->avatar;
//$this->pageURL=$this->createAbsoluteUrl('/profile/index',array('id'=>$profile->id));
$this->registerCssFile("@web/css/scores.css", [
    'depends' => [\yii\bootstrap\BootstrapAsset::class],
    'media' => 'screen',
], 'scores-theme');



?>
<!-- <center><img src="/images/logo.png" width="60%"/></center>
<hr>-->
<div class="profile-index">
  <div class="body-content">
    <div class="row">
        <div class="col-sm-8">
            <h1>Profile of <?=Html::encode($profile->owner->username)?> <?=Html::img("/images/flags/shiny/24/".$profile->country.".png",['title'=>$profile->country])?></h1>
            <p class="lead"><?=Html::encode($profile->bio)?></p>
        </div>
        <div class="col-sm-4">
            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->id==$profile->player_id && Yii::$app->user->identity->sSL !== NULL):?>
                <a href="<?=Url::to('profile/me')?>" class="pull-right"><img title="<?=Html::encode($profile->owner->username)?> Avatar" class="img-circle img-responsive" src="/images/avatars/<?=$profile->avatar?>" width="220px"></a>
                <span class="pull-right"><?php echo Html::a('<b>Download OpenVPN configuration</b>',  array('profile/ovpn'),array('class'=>'btn btn-success btn-small')); ?></span>
            <?php else:?>
                <a href="<?=Url::to(['profile/index','id'=>$profile->id])?>" class="pull-right"><img title="<?=Html::encode($profile->owner->username)?> Avatar" class="img-circle img-responsive" src="/images/avatars/<?=$profile->avatar?>" width="220px"></a>
        		<?php endif;?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <!--left col-->

            <ul class="nav nav-list">
                <li class="nav-header"><h4>Profile</h4></li>
<?php if(Yii::$app->user->id==$profile->player_id):?>
                <li><strong>Visibility</strong> <span class="pull-right"><?=$profile->visibilities[$profile->visibility]?></span></li>
                <li><strong>Spins</strong> <span class="pull-right"><abbr title="Spins today"><?=intval($playerSpin['counter'])?></abbr> / <abbr title="Total Spins"><?=$playerSpin['total']?></abbr></span></li>
<?php endif;?>
                <li><strong>Real name</strong> <span class="pull-right"><?=Html::encode($profile->owner->fullname)?></span></li>
                <li><strong>Country</strong> <span class="pull-right"><?=$profile->country?></span></li>
                <li><strong>Joined</strong> <span class="pull-right"><?=date("d.m.Y",strtotime($profile->owner->created))?></span></li>
                <li><strong>Last seen</strong> <span class="pull-right"><?=date("d.m.Y",strtotime($profile->last->on_pui))?></span></li>
                <?php if (trim($profile->twitter)):?><li><strong>Twitter</strong> <span class="pull-right"><?=Html::a('@'.Html::encode($profile->twitter),"https://twitter.com/".Html::encode($profile->twitter),['target'=>'_blank'])?></span></li><?php endif;?>
                <?php if (trim($profile->github)):?><li><strong>Github</strong> <span class="pull-right"><?=Html::a(Html::encode($profile->github),"https://github.com/".Html::encode($profile->github),['target'=>'_blank'])?></span></li><?php endif;?>
                <?php if (trim($profile->discord)):?><li><strong>Discord</strong> <span class="pull-right"><?=Html::encode($profile->discord)?></span></li><?php endif;?>
                <?php if (trim($profile->htb)):?><li><strong>HTB</strong> <span class="pull-right"><small><?=Html::a("https://hackthebox.eu/profile/".Html::encode($profile->htb),"https://hackthebox.eu/profile/".Html::encode($profile->htb),['target'=>'_blank'])?></small></span></li><?php endif;?>

            </ul>
            <br/>
            <hr/>
            <ul class="nav nav-list">
                <li class="nav-header"><h4>Activity</h4></li>
                <li><span><strong><i class="glyphicon glyphicon-signal"></i> Current Rank</strong></span> <span class="pull-right"><?=number_format($profile->rank->id)?></span></li>
                <li><span><strong><i class="glyphicon glyphicon-user"></i> Level <?=intval($profile->experience->id)?></strong></span> <span class="pull-right"><?=$profile->experience->name?></span></li>
                <li><span><strong><i class="glyphicon glyphicon-list"></i> Points</strong></span> <span class="pull-right"><?=number_format($profile->score->points)?></span></li>
                <li><span><strong><i class="glyphicon glyphicon-screenshot"></i> Headshosts</strong></span> <span class="pull-right"><?=$profile->headshotsCount?></span></li>
                <li><span><strong><i class="glyphicon glyphicon-flag"></i> Flags</strong></span> <span class="pull-right"><?php echo count($profile->owner->playerTreasures);?></span></li>
                <li><span><strong><i class="glyphicon glyphicon-fire"></i> Findings</strong></span> <span class="pull-right"><?php echo count($profile->owner->playerFindings);?></span></li>
            </ul>
        </div>
        <!--/col-3-->
        <div class="col-sm-9">

            <ul class="nav nav-tabs" id="myTab">
                <li class="active"><a href="#activity" data-toggle="tab">Activity</a></li>
                <li><a href="#progress" data-toggle="tab">Progress</a></li>
                <li><a href="#badges" data-toggle="tab">Badges</a></li>
<?php if(Yii::$app->sys->player_profile!==false && Yii::$app->user->id==$profile->player_id):?>
                <li><a href="#account-settings" data-toggle="tab">Account settings</a></li>
                <li><a href="#profile-settings" data-toggle="tab">Profile settings</a></li>
<?php endif;?>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="activity">
                    <div class="table-responsive">
                      <?php
                      Pjax::begin();
                      echo ListView::widget([
                          'id'=>'target-activity',
                          'dataProvider' => $streamProvider,
                          //'tableOptions'=>['class'=>'table table-striped'],
                          'summary'=>'<br/>',
                          'itemOptions' => [
                            'tag' => false
                          ],
                          'itemView' => '_stream',
                          'pager' => [

                          ],
                      ]);
                      Pjax::end();
                      ?>
                        <!--<table class="table table-hover">
                        </table>-->
                    </div><!--/table-resp-->

                </div>
                <!--/tab-pane-->

                <div class="tab-pane" id="progress">

                  <?= GridView::widget([
                    'id'=>'targets-grid',
                    'tableOptions'=>['class'=>'table table-striped'],
                    'summary'=>'<br/>',
                    // 'dataProvider' => new ArrayDataProvider( $profile->owner->progress, array('pagination'=>false) ),
                    'dataProvider' => $targetProgressProvider,
                    'columns' => [
                      [
                        'format'=>'raw',
                        'header'=>false,
                      'value'=>function($data){return sprintf("<b>%s <small>%s</small></b>",$data['name'],$data['ipoctet']);}
                      ],
                      [
                        'header'=>false,
                        'format'=>'raw',
                        'value'=>function($data){
                                  return sprintf('<abbr title="Flags"><i class="glyphicon glyphicon-flag"></i></abbr> %d of %d',$data['player_treasures'],$data['total_treasures']);
                                }
                      ],
                      [
                        'header'=>false,
                        'format'=>'raw',
                        'value'=>function($data){
                                  return sprintf('<abbr title="Services"><i class="glyphicon glyphicon-fire"></i></abbr> %d of %d',$data['player_findings'],$data['total_findings']);
                                }
                      ],
                      [
                        'header'=>false,
                        'format'=>'raw',
                        'value'=>function($data){
                                  return ($data['total_treasures']==$data['player_treasures'] && $data['total_findings']==$data['player_findings']) ?
                                  sprintf('<abbr title="Headshot"><i class="glyphicon glyphicon-screenshot"></i></abbr>') : "";
                                }
                      ],
                    ],
                  ]);
                  ?>
<?php
/*$this->widget('bootstrap.widgets.TbGridView', array(
	'id' => 'targets-grid',
	'htmlOptions'=>array('class'=>'span8 col-sm'),
	'type'=> 'hover',
	'dataProvider' => new CActiveDataProvider( $profile->owner->progress, array('pagination'=>false) ),
	'filter' => null,
	'summaryText'=>'',
  'enableSorting' => false,
	'columns' => array(
		[
			'type'=>'raw',
			'header'=>false,
      'value'=>function($data){return sprintf("<b>%s <small>%s</small></b>",$data->name,$data->ipoctet);}
		],
    [
					'header'=>false,
					'type'=>'raw',
					'value'=>function($data){
                      return sprintf('<abbr title="Flags"><i class="glyphicon glyphicon-large icon-flag"></i></abbr> %d of %d',$data->player_treasures,$data->total_treasures);
                      }
		],

    [
					'header'=>false,
					'type'=>'raw',
          'value'=>function($data){
                      return sprintf('<abbr title="Services"><i class="glyphicon glyphicon-fire"></i></abbr> %d of %d',$data->player_findings,$data->total_findings);
                      }
		],
    [
					'header'=>false,
					'type'=>'raw',
          'value'=>function($data){
                      return ($data->total_treasures==$data->player_treasures && $data->total_findings==$data->player_findings) ?
                        sprintf('<abbr title="Headshot"><i class="glyphicon glyphicon-screenshot"></i></abbr>') : "";
                      }
		],
    array(
			'class' => 'bootstrap.widgets.TbButtonColumn',
			'template' => '{restart} {view} {tweet}',
			'htmlOptions'=>array('style'=>'text-align: right;'),
			'buttons' => array(
				'view' => array(
					'label'=>'View',
          'url'=>'Yii::$app->createUrl("target/view", array("id"=>$data->id))',
				),
				'restart' => array(
					'label'=>'Restart',
					'icon'=>"glyphicon glyphicon-repeat",
					'click'=>"function(){return confirm('Do you really want to restart  '+$(this).parent().parent().children(':nth-child(1)').text()+'/'+$(this).parent().parent().children(':nth-child(2)').text()+'?');}",
          'url'=>'Yii::$app->createUrl("target/spin", array("id"=>$data->id))',
					'visible'=>'$data->spinable',
				),
        'tweet' => array(
					'label'=>"Tweet progress",
          'imageUrl'=>"/images/twitterBWcircle.png",
          'options'=>['class'=>'TweetThisImg'],
          'url'=>function($data){ $tweet=$this->widget('ext.ets.TweetPortlet',['model'=>$data,'type'=>'TargetProgress','profile'=>$this->curProfile,'returnContent'=>true]); return $tweet->_link; },
				),
			),
		) ,
	),
));*/?>


                </div>

                <div class="tab-pane" id="badges">

                    <h2>Coming soon</h2>

                </div>
                <!--/tab-pane-->
<?php if(Yii::$app->user->id==$profile->player_id):?>
                <div class="tab-pane" id="account-settings">
                  <?php echo $this->render('_account_settings',['model'=>$accountForm]); ?>
                </div>
                <div class="tab-pane" id="profile-settings">
                  <?php echo $this->render('_profile_settings',['model'=>$profileForm]); ?>
                </div>
                <script>
                $('#Profile_avatar').change(function() { $('#preview_avatar').attr('src','/images/avatars/'+$(this).val()); });
                </script>
<?php endif;?>

            </div>
            <!--/tab-pane-->
        </div>
        <!--/tab-content-->

    </div>
    <!--/col-9-->
  </div>
</div>
<!--/row-->
