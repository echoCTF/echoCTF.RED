<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use app\widgets\stream\StreamWidget as Stream;
$this->loadLayoutOverrides=true;

$this->title=Yii::$app->sys->event_name.' '.\Yii::t('app','Team details for').' ['.$team->name.']';
$this->_fluid="-fluid";

?>
<div class="team-view">
  <div class="body-content">
    <h2><?=\Yii::t('app','Details for team')?> [<code><?=Html::encode($team->name)?></code>]</h2>
  <?php if($team->getTeamPlayers()->count()<Yii::$app->sys->members_per_team):?>
    <p>
      <?=\Yii::t('app','Allow other players to join the team easily by providing them with this link:')?>
      <code><?=Html::a(Url::to(['/team/default/invite','token'=>$team->token],'https'),Url::to(['/team/default/invite','token'=>$team->token],'https'),['class'=>'text-bold copy-to-clipboard','swal-data'=>'Copied to clipboard!']);?></code>
      <?php if($team->owner_id===Yii::$app->user->id && \Yii::$app->cache->memcache->get('team_renewed:'.$team->id)===false):?>
        <?= Html::a('<i class="fas fa-sync-alt text-info" style="font-size: 1.2em;"></i>', Url::to(['renew','token'=>$team->token]), ['data-method' => 'POST', 'title' => 'Regenerate invite URL','rel'=>"tooltip",]) ?>
      <?php endif;?>
    </p>
  <?php else:?>
    <p class="text-warning"><?=\Yii::t('app','Your team is full, you cannot invite any more members')?></p>
  <?php endif;?>
    <hr />
    <div class="row">
      <div class="col">
        <?php
        Pjax::begin(['id'=>'stream-listing', 'enablePushState'=>false, 'linkSelector'=>'#stream-pager a', 'formSelector'=>false]);
        echo Stream::widget(['divID'=>'stream', 'dataProvider' => $streamProvider, 'pagerID'=>'stream-pager','category'=>\Yii::t('app','Latest activity of team on the platform'), 'title'=>\Yii::t('app','Team Activity Stream')]);
        Pjax::end();
        ?>
      </div>
      <div class="col-xl-4" style="min-width: 27em;max-width: 40em">
        <div class="card card-profile">
          <div class="card-avatar bg-primary">
              <img class="img" src="/images/avatars/team/<?=$team->validLogo?>?<?=\Yii::$app->formatter->asTimestamp($team->ts)?>" />
          </div>
          <div class="card-body table-responsive">
            <h4 class="card-title  orbitron"><?=Html::encode($team->name)?></h4>
            <h6 class="badge badge-primary  orbitron"><?=$team->rank !== null ? $team->rank->ordinalPlace : 'empty'?> place</h6>
            <h6 class="badge badge-secondary  orbitron"><?=$team->score !== null ? number_format($team->score->points) : 0?> points</h6>
            <p class="card-description orbitron">
              <?=Html::encode($team->description)?>
            </p>
        <?php
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'rowOptions'=>function($model){
              if($model->approved !== 1 ){
                  return ['class' => 'bg-dark text-primary'];
              }
            },
            'tableOptions'=>['class'=>'table orbitron'],
            'layout'=>'{items}',
            'summary'=>'',
            'showHeader'=>false,
            'columns' => [
              [
                'headerOptions' => ['style'=>'max-width: 35px', ],
                'label'=>null,
                'format'=>'raw',
                'value'=>function($model){
                  return Html::img('/images/avatars/'.$model->player->profile->avtr,['class'=>'rounded', 'style'=>'max-width: 30px; max-height: 30px;']);
                }
              ],
              [
                'headerOptions' => ['class'=>'d-none d-xl-table-cell', ],
                'contentOptions' => ['class'=>'d-none d-xl-table-cell'],
                'attribute'=>'player.username',
                'format'=>'raw',
                'value'=>function($model){
                    return $model->player->profile->link;
                },
                'label' => \Yii::t('app','Member')
              ],
              'player.playerScore.points:integer',
              'approved:boolean',
              [
                'class'=> 'app\actions\ActionColumn',
                'visible'=>function($model){
                  return \Yii::$app->sys->{"team_manage_members"}===true && ($team->owner_id===Yii::$app->user->id || $model->player_id!==Yii::$app->user->id );
                },
                'headerOptions' => ["style"=>'width: 4rem'],
                'template'=>'{approve} {reject}',
                'visibleButtons' => [
                  'approve' => function($model) {
                    if($model->approved===0 && Yii::$app->user->identity->teamLeader!==null)
                      return true;
                    return false;
                  },
                  'reject' => function($model) {
                    if((Yii::$app->user->identity->teamLeader!==null && Yii::$app->user->identity->teamLeader->id===$model->team_id) || ($model->player_id===Yii::$app->user->id))
                      return true;

                    return false;
                  },
                ],
                'buttons' => [
                  'approve' => function($url, $model) {
                      return Html::a(
                        '<i class="far fa-check-circle"></i>',
                          Url::to(['/team/default/approve', 'id'=>$model->id]),
                          [
                            'style'=>"font-size: 1.5em;",
                            'title' => \Yii::t('app','Approve team membership'),
                            'rel'=>"tooltip",
                            'data-pjax' => '0',
                            'data-method' => 'POST',
                            'aria-label'=>\Yii::t('app','Approve team membership'),
                            'data'=>['confirm'=>\Yii::t('app','You are about to approve this player membership!')]
                          ]
                      );
                  },
                  'reject' => function($url, $model) {
                      $msg=\Yii::t('app','Reject team membership');
                      $confirm=\Yii::t('app',"You are about to remove this player from the team!");
                      if($model->player_id===Yii::$app->user->id && Yii::$app->user->identity->teamLeader===null)
                      {
                        $confirm=\Yii::t('app',"You are about to leave this team!");
                        $msg=\Yii::t('app',"Withdraw your team membership");
                      }

                      return Html::a(
                        '<i class="far fa-trash-alt"></i>',
                          Url::to(['/team/default/reject', 'id'=>$model->id]),
                          [
                            'data-confirm'=>(Yii::$app->user->identity->teamLeader!==null && $model->player_id===Yii::$app->user->id) ? 'Are you sure you want to proceed? This action will delete the team.':null,
                            'style'=>"font-size: 1.5em;",
                            'title' => $msg,
                            'rel'=>"tooltip",
                            'data-pjax' => '0',
                            'data-method' => 'POST',
                            'aria-label'=>$msg,
                            'data'=>[
                              'confirm'=>$confirm
                            ]
                          ]
                      );
                  },
              ],
            ]
          ]
        ]);
        ?>
          <?php if(Yii::$app->user->identity->teamLeader!==null && $team->owner_id===Yii::$app->user->id):?><?=Html::a(\Yii::t('app','Update'),['/team/default/update'],['class'=>'btn btn-primary text-dark text-bold d-block'])?><?php endif;?>
          </div>

        </div>
      </div>

    </div>
<?php
if((Yii::$app->sys->team_visible_instances===true || intval($teamInstanceProvider->count)>0) && Yii::$app->user->identity->teamPlayer && $team->id===Yii::$app->user->identity->teamPlayer->team_id)
{
  echo '<h3>',\Yii::t('app','Team instances'),'<h3>';
  echo ListView::widget([
    'summary'=>false,
    'showOnEmpty'=>false,
    'emptyText'=>false,
    'options'=>['class'=>'row'],
    'itemOptions'=>['class'=>'col-xl-3'],
    'dataProvider' => $teamInstanceProvider,
    'itemView' => '_target_instance_card',
  ]);
}
?>
  </div>
</div>
