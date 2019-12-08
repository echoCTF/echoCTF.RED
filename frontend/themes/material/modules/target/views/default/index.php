<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;
use rce\material\widgets\Card;
//die(var_dump($userTarget));
$this->title = Yii::$app->sys->event_name .' - Target: '.$target->name;
#$this->pageDescription=CHtml::encode($target->purpose);
#$this->pageImage=Yii::app()->getBaseUrl(true)."/images/targets/".$target->name.".png";
#$this->pageURL=$this->createAbsoluteUrl('target/view',array('id'=>$target->id));
#Yii::$app->clientScript->registerCssFile(Yii::app()->baseUrl . '/css/scores.css');
$this->registerCssFile("@web/css/scores.css", [
    'depends' => [\yii\bootstrap\BootstrapAsset::className()],
    'media' => 'screen',
], 'scores-theme');
$percentage=(($userTarget->player_findings+$userTarget->player_treasures)*100)/($userTarget->total_treasures+$userTarget->total_findings);
?>

<div class="target-index">
  <div class="body-content">
    <div class="row">
          <div class="col-lg-4 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>sprintf('<img src="/images/targets/_%s.png" height="60"/>',$target->name),
                'color'=>'primary',
                'subtitle'=>sprintf("%s, %s", $target->difficultyText,boolval($target->rootable) ? "rootable" : "non rootable"),
                'title'=>sprintf('%s / %s',$target->name,long2ip($target->ip)),
                'footer'=>sprintf('<div class="stats">%s</div>',$target->purpose),
            ]);
            echo "<p class='text-danger'><i class='material-icons'>flag</i> ", count($target->treasures)," / ";
            echo "<i class='material-icons'>whatshot</i> ", count($target->findings),"<br/>",number_format($target->points), " pts</p>";
            Card::end(); ?>
          </div>
          <div class="col-lg-4 col-md-6 col-sm-6">
            <div  style="line-height: 1; font-size: 12vw; vertical-align: bottom;text-align: center;">
              <?=$percentage==100 ? '<i class="material-icons" style="font-size: 10vw">done_all</i>':'&#8800;'?>
            </div>
            <div class="progress">
                <div class="progress-bar <?=$percentage==100 ? 'bg-success':'bg-danger'?>" style="width: <?=$percentage?>%" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"><?=$percentage==100 ? '#Headshot': number_format($percentage).'%'?></div>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>sprintf('<img src="/images/avatars/%s" height="60"/>',Yii::$app->user->identity->profile->avatar),
                'color'=>'primary',
                'subtitle'=>Html::encode('Level '.Yii::$app->user->identity->profile->experience->id.' / '.Yii::$app->user->identity->profile->experience->name),
                'title'=>Html::encode(Yii::$app->user->identity->username)." / ".long2ip(Yii::$app->user->identity->profile->last->vpn_local_address),
                'footer'=>sprintf('<div class="stats">%s</div>',Html::encode(Yii::$app->user->identity->profile->bio)),
            ]);
            echo "<p class='text-primary '><i class='material-icons'>flag</i> ", $userTarget->player_treasures," / ";
            echo "<i class='material-icons'>whatshot</i> ", $userTarget->player_findings,"<br/>",number_format($playerPoints)," pts<br/>";
            Card::end(); ?>
          </div>

      </div>

    <div class="card">
      <div class="card-header card-header-primary">
        <h4 class="card-title">Target: <?=$target->name?> <small><?=$target->schedule?></small></h4>
        <p class="card-category"><?=$target->purpose?></p>
      </div>
      <div class="card-body table-responsive">
    <?= DetailView::widget([
      'id'=>'target-fulldetails',
      'model' => $target,
      'options'=>['class'=>'table table-striped table-condenced detail-view'],
      'attributes' => [
        'id',
        'fqdn',
        [
          'attribute'=>'ip',
          'value'=>long2ip($target->ip)
        ],
    		[
    			'attribute'=>'difficulty',
    			'value'=>$target->difficultyText,
    		],
        'rootable:boolean',
        [
          'label'=>'Total points',
    			'type'=>'number',
          'value'=>$target->points,
        ],
        [
          'label'=>'Flags / Services',
          'format'=>'raw',
          'value'=>'<i class="glyphicon glyphicon-flag"></i> '.count($target->treasures).' / <i class="glyphicon glyphicon-fire"></i> '.count($target->findings) ,
        ],

    		[
          'label'=>'Headshots',
    			'format'=>'raw',
    			'value'=>function($model){
  									$headshots=null;
  									foreach($model->headshots as $player)
                      if((int)$player->active===1)
  											$headshots[]=$player->profile->link;
  								if ($headshots===NULL) return "None";
  								return implode(", ",$headshots);
  							}
        ],
        [
          'attribute'=>'description',
          'label'=>false,
          'format'=>'html',
        ],
      ],
  ]) ?>
    <?php /* echo ListView::widget([
        'id'=>'target-headshots',
        'dataProvider' => $headshotsProvider,
        'options'=>['class'=>"Leaderboard col-md-3","style"=>"padding-top: 0em; margin-top: -4em"],
        'summary'=>'<h3>'.$target->countHeadshots.' Headshots</h3>',
        'itemOptions' => [
          'tag' => false
        ],
        'itemView' => '_headshot',
        'viewParams'=>['totalPoints'=>$target->points]
    ]);*/?>
  </div>
  </div>

      <h3>Latest target activity</h3>
      <?php \yii\widgets\Pjax::begin(); ?>
      <?php echo ListView::widget([
          'id'=>'target-activity',
          'dataProvider' => $streamProvider,
          'summary'=>false,
          'itemOptions' => [
            'tag' => false
          ],
          'itemView' => '_stream',
          'pager' => [

          ],
      ]);?>
      <?php \yii\widgets\Pjax::end(); ?>
</div>
</div>
