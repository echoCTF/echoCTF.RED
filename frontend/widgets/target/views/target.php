<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\Twitter;
?>
<div class="card bg-dark">
  <div class="card-header card-header-danger">
    <h4 class="card-title"><?=$TITLE?></h4>
    <p class="card-category"><?=$CATEGORY?></p>
  </div>
  <div class="card-body table-responsive">
<?php
echo GridView::widget([
    'id'=>$divID,
    'dataProvider' => $dataProvider,
    'emptyText'=>'<div class="card-body"><b class="text-info">No targets exist for the given criteria...</b></div>',
    'rowOptions'=> function ($model, $key, $index, $grid) {
          return;
          return ['data-id' => $model->id,'class'=>'clickable-row','data-href'=>Url::to(['/target/default/view', 'id'=>$model->id])];
    },
    'pager'=>[
      'class'=>'yii\bootstrap4\LinkPager',
      'linkOptions'=>['class' => ['page-link'], 'aria-label'=>'Pager link','rel'=>'nofollow'],
      'options'=>['id'=>'target-pager', 'class'=>'align-middle'],
      'firstPageLabel' => '<i class="fas fa-step-backward"></i>',
      'lastPageLabel' => '<i class="fas fa-step-forward"></i>',
      'maxButtonCount'=>3,
      'disableCurrentPageButton'=>true,
      'prevPageLabel'=>'<i class="fas fa-chevron-left"></i>',
      'nextPageLabel'=>'<i class="fas fa-chevron-right"></i>',
    ],
    'tableOptions'=>['class'=>'table table-xl','style'=>"line-height: 1em;"],
    'layout'=>$layout,
    'summary'=>$summary,
    'columns' => [
      [
        'label'=>'',
        'contentOptions' => ['class' => 'text-center'],
        'headerOptions' => ['class' => 'text-center', "style"=>'width: 1.5em'],
        'format'=>'raw',
        'value'=>function($model) {
        return sprintf('<img src="%s" alt="%s" width="25" height="25" class="target-thumbnail rounded">', $model->thumbnail, $model->fqdn);
        }
      ],
      [
        'headerOptions' => ['class'=>'d-none d-xl-table-cell', ],
        'contentOptions' => ['class'=>'d-none d-xl-table-cell'],
        'attribute'=>'id',
        'visible'=>!in_array('id', $hidden_attributes),
      ],
      [
        'visible'=>!in_array('name', $hidden_attributes),
        'attribute'=>'name',
        'headerOptions' => ["style"=>'width: 8vw;'],
        'contentOptions'=> ["style"=>'width: 8vw;'],
        'format'=>'raw',
        'label'=>'Target',
        'value'=>function($model) {
          $append="";
          //if($model->network)
          //  $append=" ". $model->network->icon;
          if($model->status === 'powerup')
            $append=sprintf(' <abbr title="Scheduled for powerup at %s"><i class="fas fa-arrow-alt-circle-up text-secondary"></i></abbr>', \Yii::$app->formatter->asDatetime($model->scheduled_at,'long'));
          else if($model->status === 'powerdown')
            $append=sprintf(' <abbr title="Scheduled for powerdown at %s"><i class="fas fa-arrow-alt-circle-down"></i></abbr>', $model->scheduled_at);

          if(!Yii::$app->user->isGuest && Yii::$app->user->identity->getPlayerHintsForTarget($model->id)->count() > 0)
            $append.=' <sup><abbr title="You have hints on the target"><i class="fas fa-lightbulb text-primary" aria-hidden="true"></i></abbr></sup>';
          if($model->created_at!==NULL && strtotime($model->created_at) >= strtotime('-'.intval(Yii::$app->sys->target_days_new).' days'))
          {
            $append.=' <sup><small class="text-danger">new</small></sup>';
          }
          elseif(strtotime($model->ts) >= strtotime('-'.intval(Yii::$app->sys->target_days_updated).' days'))
          {
            $append.=' <sup><small class="text-danger">updated</small></sup>';
          }

          return Html::a(Html::encode($model->name), ['/target/default/view', 'id'=>$model->id]).$append;
        }
      ],
      [
        'visible'=>!in_array('ip', $hidden_attributes),
        'attribute'=>'ip',
        'label'=>'IP',
        'headerOptions' => ["style"=>'width: 2vw;' /*, 'class'=>'d-none d-lg-table-cell'*/],
        'contentOptions'=> ["style"=>'width: 2vw;' /*, 'class'=>'d-none d-lg-table-cell'*/],
        'format'=>'raw',
        'value'=>function($model) { if($model->on_ondemand && $model->ondemand_state===-1) return '<abbr title="System is powered down">0.0.0.0</abbr>'; else return long2ip($model->ip);}
      ],
      [
        'visible'=>!in_array('writeup', $hidden_attributes),
        'format'=>'raw',
        'headerOptions' => ['class' => 'text-center', "style"=>'width: 2rem'],
        'contentOptions' => ['class' => 'text-center'],
        'encodeLabel'=>false,
        'label'=>false,
        //'label'=>'<abbr title="Target has writeups or not?"><i class="fa fa-question-circle" aria-hidden="true"></i></abbr>',
        'value'=>function($model) {return $model->approved_writeups === 0 ? '' : '<abbr title="Writeups available"><i class="fas fa-book text-primary" style="font-size: 1.2em;"></i></abbr>';},
      ],
      [
        'visible'=>!in_array('difficulty', $hidden_attributes),
        'attribute'=>'difficulty',
        'format'=>'raw',
        'encodeLabel'=>false,
        'label'=>'<abbr title="Difficulty rating"><i class="fa fa-battery-full" aria-hidden="true"></i></abbr>',
        'contentOptions' => ['class' => 'd-none d-xl-table-cell text-center',],
        'headerOptions' => ['class' => 'text-center d-none d-xl-table-cell',"style"=>'width: 2em'],
        'value'=>function($model) {

          $bgcolor="";
          $abbr="Ours: ".ucfirst($model->difficultyText).", Players: ".ucfirst($model->getDifficultyText(round($model->player_rating)));
          switch($model->difficulty)
          {
            case 0:
             $icon='fa-battery-empty';
              $bgcolor="text-gray";
              break;
            case 1:
             $icon='fa-battery-quarter';
              $bgcolor='red-success';
              break;
            case 2:
              $icon='fa-battery-half';
              $bgcolor="text-secondary";
              break;
            case 3:
             $icon='fa-battery-three-quarters';
              $bgcolor="text-warning";
              break;
            case 4:
             $bgcolor="text-danger";
              $icon='fa-battery-full';
              break;
            default:
             $icon='fa-battery-full';
          }
          return sprintf('<abbr title="%s"><i class="fas %s %s" style="font-size: 1.35vw;"></i></abbr>', $abbr, $icon, $bgcolor);
        },
      ],
      [
        'visible'=>!in_array('rootable', $hidden_attributes),
        'attribute'=>'rootable',
        'format'=>'raw',
        'headerOptions' => ['class' => 'text-center', "style"=>'width: 4rem'],
        'contentOptions' => ['class' => 'text-center'],
        'encodeLabel'=>false,
        'label'=>'<abbr title="Target rootable or not?"><i class="fa fa-hashtag" aria-hidden="true"></i></abbr>',
        'value'=>function($model) {return intval($model->rootable) == 0 ? '' : '<abbr title="Rootable"><i class="fa fa-hashtag"></i></abbr>';},
      ],
      [
        'visible'=>!in_array('total_findings', $hidden_attributes),
        'format'=>'raw',
        'encodeLabel'=>false,
        'headerOptions' => ["style"=>'width: 4rem', 'class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'label'=>'<abbr title="Services"><i class="fa fa-fingerprint" aria-hidden="true"></i></abbr>',
        'attribute'=>'total_findings',
        'value'=>function($model) { return sprintf('<i class="fas fa-fingerprint"></i> %d<small>/%d</small>', $model->total_findings, $model->player_findings);},
      ],
      [
        'visible'=>!in_array('total_treasures', $hidden_attributes),
        'format'=>'raw',
        'encodeLabel'=>false,
        'headerOptions' => ["style"=>'width: 4rem', 'class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'label'=>'<abbr title="Flags"><i class="fa fa-flag" aria-hidden="true"></i></abbr>',
        'attribute'=>'total_treasures',
        'value'=>function($model) { return sprintf('<i class="fas fa-flag"></i> %d<small>/%d</small>', $model->total_treasures, $model->player_treasures);},
      ],
      [
        'visible'=>!in_array('headshots', $hidden_attributes),
        'format'=>'raw',
        'encodeLabel'=>false,
        'headerOptions' => ["style"=>'width: 6rem', 'class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center',],
        'attribute'=>'headshots',
        'label'=>'<abbr title="Number of users who owned all flags and services: Headshots"><i class="fas fa-skull"></i></abbr>',
        'value'=>function($model) {
          $msg=sprintf("%d user%s have managed to headshot this target", $model->total_headshots, $model->total_headshots > 1 ? 's' : '');
          if($model->total_treasures == $model->player_treasures && $model->player_findings == $model->total_findings)
          {
            return '<abbr title="'.$msg.'"><i class="fas fa-skull text-primary"></i></abbr> '.$model->total_headshots;
          }
          return '<abbr title="'.$msg.'"><i class="fas fa-skull"></i></abbr> '.$model->total_headshots;},
      ],
      [
        'visible'=>!in_array('progress', $hidden_attributes),
        'format'=>'raw',
        'encodeLabel'=>false,
        'headerOptions' => ['class'=>'text-center d-none d-xl-table-cell', ],
        'contentOptions' => ['class'=>'text-center d-none d-xl-table-cell'],
        'attribute'=>'progress',
        'label'=>'Progress',
        'value'=>function($model) {
          return '<center>'.yii\bootstrap4\Progress::widget(['percent' => intval(floor($model->progress)), 'label' => false, 'barOptions' => ['class' => 'bg-gradual-progress','aria-label'=>"Progress on target: {$model->progress}%"],'options'=>['class'=>'bg-dark','style'=>'border: 1px solid #cccccc; height: 1em; max-width: 18rem;']]).'</center>';
        },
      ],
      [
        'class'=> 'app\actions\ActionColumn',
        'visible'=>!in_array('ActionColumn', $hidden_attributes),
        'headerOptions' => ["style"=>'width: 2rem'],
        'template'=>$buttonsTemplate,
        'buttons' => [
          'spin' => function($url, $model) {
              return Html::a(
                '<i class="fas fa-power-off"></i>',
                  Url::to(['/target/default/spin', 'id'=>$model->id]),
                  [
                    'style'=>"font-size: 1.5em;",
                    'title' => 'Request target Restart',
                    'rel'=>"tooltip",
                    'data-pjax' => '0',
                    'data-method' => 'POST',
                    'aria-label'=>'Request target Restart',
                  ]
              );
          },
          'tweet' => function($url, $model) {
              $url=Url::to(['/target/default/view', 'id'=>$model->id], 'https');

              if(!Yii::$app->user->isGuest && Yii::$app->user->id === $this->context->player_id)
              {
                if($model->total_treasures === $model->player_treasures && $model->total_findings === $model->player_findings)
                  return Twitter::widget(['message'=>'Hey check this out, I headshotted '.strip_tags($model->name), 'url'=>$url, 'linkOptions'=>['class'=>'twitterthis', 'target'=>'_blank', 'style'=>'font-size: 1.5em', 'rel'=>"noreferrer"]]);
                elseif($model->player_treasures !== 0 || $model->player_findings !== 0)
                  return Twitter::widget(['message'=>sprintf('Hey check this out, i have found %d out of %d flags and %d out of %d services on %s', $model->player_treasures, $model->total_treasures, $model->player_findings, $model->total_findings, $model->name), 'url'=>$url, 'linkOptions'=>['class'=>'twitterthis', 'target'=>'_blank', 'style'=>'font-size: 1.5em', 'rel'=>"noreferrer"]]);

              }
              if($this->context->profile !== null)
              {
                $url=Url::to($this->context->profile->linkTo, 'https');
                if($model->total_treasures === $model->player_treasures && $model->total_findings === $model->player_findings)
                  return Twitter::widget(['message'=>sprintf('Hey check this out, %s headshotted %s', $this->context->profile->twitterHandle, $model->name), 'url'=>$url, 'linkOptions'=>['class'=>'twitterthis', 'target'=>'_blank', 'style'=>'font-size: 1.5em', 'rel'=>"noreferrer"]]);

                return Twitter::widget(['message'=>sprintf('Hey check this out, %s found %d out of %d flags and %d out of %d services on %s', $this->context->profile->twitterHandle, $model->player_treasures, $model->total_treasures, $model->player_findings, $model->total_findings, $model->name), 'url'=>$url, 'linkOptions'=>['class'=>'twitterthis', 'target'=>'_blank', 'style'=>'font-size: 1.5em', 'rel'=>"noreferrer"]]);
              }
              return Twitter::widget(['message'=>sprintf('Hey check this target [%s], %s', $model->name, $model->purpose), 'url'=>$url, 'linkOptions'=>['class'=>'twitterthis', 'target'=>'_blank', 'style'=>'font-size: 1.5em', 'rel'=>"noreferrer"]]);
          },
          'view' => function($url, $model) {
            if($this->context->profile !== null)
              return Html::a(
                '<i class="fas fa-eye"></i>',
                  Url::to(['/target/default/versus', 'id'=>$model->id, 'profile_id'=>$this->context->profile->id]),
                  [
                    'style'=>"font-size: 1.5em;",
                    'rel'=>"tooltip",
                    'title' => 'View target vs player card',
                    'aria-label'=>'View target vs player card',
                    'data-pjax' => '0',
                  ]
              );
              return Html::a(
                '<i class="fas fa-eye"></i>',
                  Url::to(['/target/default/view', 'id'=>$model->id]),
                  [
                    'style'=>"font-size: 1.5em;",
                    'rel'=>"tooltip",
                    'title' => 'View target details',
                    'aria-label'=>'View target details',
                    'data-pjax' => '0',
                  ]
              );
          }
      ],
      'visibleButtons' => [
        'spin' => function($model) {
            if(Yii::$app->user->isGuest || $this->context->personal === true)
              return false;
            return $model->spinable;
          },
      ]

    ]
    ],
]);
?>
  </div>
</div>
