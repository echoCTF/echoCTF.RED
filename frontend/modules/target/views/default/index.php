<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;

$this->title = Yii::$app->sys->event_name .' - Target: '.$target->name;
#$this->pageDescription=CHtml::encode($target->purpose);
#$this->pageImage=Yii::app()->getBaseUrl(true)."/images/targets/".$target->name.".png";
#$this->pageURL=$this->createAbsoluteUrl('target/view',array('id'=>$target->id));
#Yii::$app->clientScript->registerCssFile(Yii::app()->baseUrl . '/css/scores.css');
$this->registerCssFile("@web/css/scores.css", [
    'depends' => [\yii\bootstrap\BootstrapAsset::className()],
    'media' => 'screen',
], 'scores-theme');
?>

<div class="target-index">
  <div class="body-content">

  <h2>Target: <?php echo $target->name; ?> #<?php echo $target->id; ?> <small><?=$target->schedule?></small></h2>
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
  									foreach($model->headshots as $player) {
  									                      if((int)$player->active===1)
  											$headshots[]=$player->profile->link;
  									}
  								if ($headshots===NULL) {
  								    return "None";
  								}
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

<h3>Latest activity</h3>
<hr />
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
