<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ListView;
use rce\material\widgets\Card;
use yii\widgets\Pjax;
$this->_fluid="-fluid";
$this->title = Yii::$app->sys->event_name .' - Dashboard';
$this->registerCssFile("@web/css/scores.css", [
    'depends' => [\yii\bootstrap\BootstrapAsset::className()],
    'media' => 'screen',
], 'scores-theme');
?>
<div class="dashboard-index">
  <div class="body-content">
    <!-- XXX FIXME XXX ADD MISSING ONLINE COUNTERS -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="material-icons">flag</i>',
                'color'=>'danger',
                'title'=>'49/50 <small></small>',
                'subtitle'=>'Claimed/Total Flags',
                'footer'=>'<div class="stats">
                        <i class="material-icons text-danger">flag</i>
                        <a href="#">You have 14 claimed</a>
                      </div>',
            ]); Card::end(); ?>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="material-icons">memory</i>',
                'color'=>'success',
                'title'=>'127',
                'subtitle'=>'Headshots',
                'footer'=>'<div class="stats">
                        <i class="material-icons">memory</i> You have 13 headshots
                      </div>',
            ]); Card::end(); ?>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <?php Card::begin([
                'header'=>'header-icon',
                'type'=>'card-stats',
                'icon'=>'<i class="material-icons">widgets</i>',
                'color'=>'danger',
                'title'=>'123,123',
                'subtitle'=>'Points',
                'footer'=>'<div class="stats">
                        <i class="material-icons">memory</i> You have 12300 points
                      </div>',
            ]); Card::end(); ?>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <?php Card::begin([
                'type'=>'card-stats',
                'header'=>'header-icon',
                'icon'=>'<i class="fa fa-twitter"></i>',
                'color'=>'info',
                'title'=>'+245',
                'subtitle'=>'Followers',
                'footer'=>'<div class="stats">
                        <i class="material-icons">update</i> Just Updated
                      </div>',
            ]); Card::end(); ?>
        </div>
    </div>


    <div class="row">
      <?php
       echo GridView::widget([
      		'id'=>'target-list',
          'pager'=>[
            'options'=>['class'=>'pagination'],
            'pageCssClass'=>'page-item',
            'linkOptions'=>['class'=>'page-link'],
          ],
          'options'=>['class'=>'card col-sm-8'],
      		'tableOptions'=>['class'=>'table'],
          'layout'=>'{summary}<div class="card-body table-responsive">{items}<nav aria-label="Target Navigation">{pager}</nav></div>',
          'dataProvider' => $targetProvider,
      		'summary'=>'<div class="card-header card-header-primary"><h4 class="card-title">Target list</h4><p class="card-category">List of currently available targets</p></div>',
          'columns' => [
            [
      				'attribute'=>'name',
      				'label'=>'',
              'format'=>'raw',
              'value'=>function($model){return sprintf('<img src="/images/targets/_%s.png" alt="%s" height="32px" style="max-width: 32px;">',$model->name, $model->fqdn);}
      			],
      			[
      				'attribute'=>'name',
      				'header'=>'Target',
      			],
      			[
      				'attribute'=>'ip',
      				'header'=>'IP',
      				'value'=>function($model){return long2ip($model->ip);}
      			],
      			[
      				'attribute'=>'difficultyText',
      				'header'=>'<abbr title="Difficulty rating">Difficulty</abbr>'
      			],
      			[
      				'header'=>'<center><abbr title="Flags"><i class="fa fa-flag" aria-hidden="true"></i></abbr> / <abbr title="Services"><i class="fa fa-fire" aria-hidden="true"></i></abbr> / <abbr title="Number of users who owned all flags and services: Headshots"><i style="vertical-align: middle;font-size: 1.2em;" class="material-icons">memory</i></abbr></center>',
      				'format'=>'raw',
      				//'attribute'=>'formattedExtras'
              'value'=>function($model){
                $scheduled=$ret="";
                if(intval($model->active)===1 && $model->status==='powerdown')
                  $scheduled=sprintf('<abbr title="Scheduled to powedown at %s"><i class="glyphicon glyphicon-hand-down"></i></abbr>',$model->scheduled_at);
                elseif(intval($model->active)===0  && $model->status==='powerup' )
                  $scheduled=sprintf('<abbr title="Scheduled to powerup %s"><i class="glyphicon glyphicon-hand-up"></i></abbr>',$model->scheduled_at);

                if($model->rootable==1)
                  $ret=sprintf('<abbr title="Target is rootable"><i style="font-size: 0.898em;" class="fa fa-hashtag" aria-hidden="true"></i></abbr> /');
                return sprintf("<center>%s <abbr title='Flags'><i class='material-icons'>flag</i>%d</abbr> / <abbr title='Service'><i class='material-icons'>whatshot</i>%d</abbr> / <abbr title='Headshots'><i class='material-icons'>memory</i>%d</abbr> %s</center>",$ret,count($model->treasures),count($model->findings),count($model->getHeadshots()),$scheduled);

              }
      			],
      			[
              //<a  href="/dashboard/delete?id=11" title="Delete" aria-label="Delete" data-confirm="Are you sure you want to delete this item?" data-method="post" data-pjax="0"><i class="material-icons">delete</i></a>
            //  'class' => 'yii\grid\ActionColumn',
              'class'=> 'rce\material\grid\ActionColumn',
      				'template'=>'{view} {spin}',
      				'buttons' => [
      					'spin' => function ($url,$model) {
      							return Html::a(
      									'<i class="material-icons large">power_settings_new</i>',
      									Url::to(['/target/default/spin','id'=>$model->id]),
      									[
                          //'class'=>"btn btn-primary btn-round btn-simple btn-xs",
                          'style'=>"font-size: 1.5em;",
    											'title' => 'Restart container',
    											'data-pjax' => '0',
    											'data-method' => 'POST',
      									]
      							);
      					},
                'view' => function ($url,$model) {
      							return Html::a(
                      '<i class="material-icons">remove_red_eye</i>',
      									Url::to(['/target/default/index','id'=>$model->id]),
      									[
                          'style'=>"font-size: 1.5em;",
                          'rel'=>"tooltip",
//                          'class'=>"btn btn-primary btn-round btn-simple btn-xs",
                          'title' => 'View target',
      										'data-pjax' => '0',
      									]
      							);
      					}
      			],
      			'visibleButtons' => [
          		'spin' => function ($model) {
      						return $model->spinable;
      					},
      			]

      		]
          ],
      ]);
      ?>
      <div class="col-sm-4">
        <?php Pjax::begin();
        echo ListView::widget([
            'id'=>'Leaderboard',
            'options'=>['class'=>'card'],
            'dataProvider' => $scoreProvider,
            'layout'=>'{summary}<div class="card-body table-responsive">{items}</div><div class="card-footer">{pager}</div>',
            'summary'=>'<div class="card-header card-header-primary"><h4 class="card-title">Top 10</h4><p class="card-category">List of top 10 players by points</p></div>',
            'itemOptions' => [
              'tag' => false
            ],
            'itemView' => '_score',
            'viewParams'=>[
              'totalPoints'=>$totalPoints,
            ]
        ]);Pjax::end();
?>
      </div>
    </div><!-- //row -->
    <?php Pjax::begin();
    echo ListView::widget([
        'id'=>'target-activity',
        'options'=>['class'=>'card'],
        'dataProvider' => $streamProvider,
        'pager'=>[
          'options'=>['class'=>'pagination'],
          'pageCssClass'=>'page-item',
          'linkOptions'=>['class'=>'page-link'],
        ],
        'layout'=>'{summary}<div class="card-body">{items}</div><div class="card-footer">{pager}</div>',
        'summary'=>'<div class="card-header card-header-primary"><h4 class="card-title">Activity Stream</h4><p class="card-category">Latest activity on the platform</p></div>',
        'itemOptions' => [
          'tag' => false
        ],
        'itemView' => '_stream',
    ]); Pjax::end();

    ?>

  </div><!-- //body-content -->
</div>
