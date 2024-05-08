<?php

namespace app\modules\team\controllers;

use Yii;
use app\modules\team\models\Team;
use app\modules\team\models\TeamPlayer;
use app\modules\team\models\TeamSearch;
use app\modules\team\models\CreateTeamForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * TeamController implements the CRUD actions for Team model.
 */
class DefaultController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
      $parent=parent::behaviors();
        return ArrayHelper::merge($parent,[
          'access' => [
                'class' => \yii\filters\AccessControl::class,
                'only' => ['index', 'create', 'join', 'update', 'approve', 'reject', 'invite', 'mine','view','renew'],
                'rules' => [
                  'teamsAccess'=>[
                    'actions'=>[''],
                  ],
                  'eventStartEnd'=>[
                    'actions' => [''],
                  ],
                  'eventStart'=>[
                    'actions' => [''],
                  ],
                  'eventEnd'=>[
                    'actions' => ['join','update','approve','reject','create'],
                  ],
                  [
                    'actions'=>['create', 'join', 'update', 'approve', 'reject'],
                    'allow'=> false,
                    'roles' => ['@'],
                    'matchCallback' => function ($rule, $action) {
                      return (\Yii::$app->sys->{"team_manage_members"}===false);
                    },
                    'denyCallback' => function() {
                      \Yii::$app->session->addFlash('error', \Yii::t('app','Team management is disabled at the moment.'));
                      return $this->redirect(['index']);
                    }
                  ],
                  [
                    'actions'=> ['create'],
                    'allow'=> false,
                    'roles' => ['@'],
                    'matchCallback' => function ($rule, $action) {
                      return (\Yii::$app->user->identity->teamLeader!==null || \Yii::$app->user->identity->team!==null);
                    },
                    'denyCallback' => function() {
                      \Yii::$app->session->setFlash('error', \Yii::t('app','You are already a member of a team.'));
                      return $this->redirect(['/team/mine']);
                    }
                  ],
                  [ // Only join when not on team
                    'actions'=> ['join'],
                    'allow'=> false,
                    'roles' => ['@'],
                    'matchCallback' => function ($rule, $action) {
                      return \Yii::$app->user->identity->team!==null;
                    },
                    'denyCallback' => function () {
                      \Yii::$app->session->setFlash('error', \Yii::t('app','You are already a member of a team.'));
                      return $this->redirect(['/team/mine']);
                    }
                  ],
                  [ // Only allow updates from teamLeaders
                    'actions'=> ['update','renew'],
                    'allow' => false,
                    'roles' => ['@'],
                    'matchCallback' => function ($rule, $action) {
                      return \Yii::$app->user->identity->teamLeader===null;
                    },
                    'denyCallback' => function () {
                      \Yii::$app->session->setFlash('error', \Yii::t('app','You are not the leader of any teams.'));
                      return $this->redirect(['/team/mine']);
                    }
                  ],
                  [
                     'actions' => ['reject'],
                     'allow' => false,
                     'roles' => ['@'],
                     'matchCallback' => function ($rule, $action) {
                         return \Yii::$app->sys->event_start!==false && (time()>=\Yii::$app->sys->event_start && time()<=\Yii::$app->sys->event_end);
                     },
                     'denyCallback' => function () {
                       \Yii::$app->session->setFlash('info', \Yii::t('app','These actions are disabled during the competition'));
                       return  \Yii::$app->getResponse()->redirect(['/team/mine']);
                     }
                 ],
                 'disabledRoute'=>[
                     'allow' => false,
                     'matchCallback' => function ($rule, $action) {
                       return \Yii::$app->DisabledRoute->disabled($action);
                     },
                     'denyCallback' => function () {
                       throw new \yii\web\HttpException(404,('This area is disabled.'));
                     },
                 ],
                 [
                   'actions' => ['index', 'mine', 'create', 'join', 'update', 'approve', 'reject', 'invite', 'view','renew'],
                   'allow' => true,
                   'roles' => ['@'],
                 ],
              ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'reject' => ['POST'],
                    'approve' => ['POST'],
                    'join' => ['POST'],
                    'renew' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Shows team model.
     * @return mixed
     */
    public function actionView($token)
    {
      $model=$this->findModel(['token'=>$token]);
      $TP=TeamPlayer::find()->where(['team_id'=>$model->id])->orderBy(['ts' => SORT_ASC]);
      $dataProvider = new ActiveDataProvider([
          'query' => TeamPlayer::find()->where(['team_id'=>$model->id])->orderBy(['ts' => SORT_ASC]),
          'sort' =>false,
          'pagination' => false,
      ]);
      $teamPlayers = ArrayHelper::getColumn($TP->all(),'player_id');
      $teamInstances = \app\modules\target\models\TargetInstance::find()->leftJoin('team_player','target_instance.player_id=team_player.player_id')
          ->andFilterWhere(['in','target_instance.player_id',$teamPlayers])
          ->andFilterWhere(['team_player.approved'=>1]);
      if(\Yii::$app->sys->team_visible_instances!==true)
      {
        $teamInstances->andFilterWhere(['team_allowed'=>1]);
      }
      $teamInstanceProvider=new ActiveDataProvider([
        'query' => $teamInstances,
        'pagination' => [
            'pageSizeParam'=>'teamInstance-perpage',
            'pageParam'=>'teamInstance-page',
            'pageSize' => Yii::$app->sys->members_per_team === false ? 10 : Yii::$app->sys->members_per_team,
        ]
      ]);

      $stream=\app\models\Stream::find()->select('stream.*,TS_AGO(ts) as ts_ago')
      ->where(['player_id'=>$teamPlayers])
      ->orderBy(['ts'=>SORT_DESC, 'id'=>SORT_DESC]);
      $streamProvider=new ActiveDataProvider([
            'query' => $stream,
            'pagination' => [
                'pageSizeParam'=>'stream-perpage',
                'pageParam'=>'stream-page',
                'pageSize' => 10,
            ]
      ]);

      return $this->render('view',[
        'team'=>$model,
        'teamInstanceProvider'=>$teamInstanceProvider,
        'streamProvider'=>$streamProvider,
        'dataProvider'=>$dataProvider,
      ]);
    }
    /**
     * Shows current team membership.
     * @return mixed
     */
    public function actionMine()
    {
      if(Yii::$app->user->identity->team===null || Yii::$app->user->identity->teamPlayer===null)
      {
        return $this->redirect(['index']);
      }
      $dataProvider = new ActiveDataProvider([
          'query' => TeamPlayer::find()->where(['team_id'=>Yii::$app->user->identity->team->id])->orderBy(['ts' => SORT_ASC]),
          'sort' =>false,
          'pagination' => false,
      ]);
      $teamPlayers = ArrayHelper::getColumn(Yii::$app->user->identity->team->players,'id');
      $teamInstances = \app\modules\target\models\TargetInstance::find()->leftJoin('team_player','target_instance.player_id=team_player.player_id')
        ->andFilterWhere(['team_instance.player_id'=>$teamPlayers])
        ->andFilterWhere(['team_player.approved'=>1]);

      if(\Yii::$app->sys->team_visible_instances!==true)
      {
        $teamInstances->andFilterWhere(['team_allowed'=>1]);
      }
      $teamInstanceProvider=new ActiveDataProvider([
        'query' => $teamInstances,
        'pagination' => [
            'pageSizeParam'=>'teamInstance-perpage',
            'pageParam'=>'teamInstance-page',
            'pageSize' => Yii::$app->sys->members_per_team === false ? 10 : Yii::$app->sys->members_per_team,
        ]
      ]);

      $stream=\app\models\Stream::find()->select('stream.*,TS_AGO(ts) as ts_ago')
          ->where(['stream.player_id'=>$teamPlayers])
          ->orderBy(['ts'=>SORT_DESC, 'id'=>SORT_DESC]);
      $streamProvider=new ActiveDataProvider([
            'query' => $stream,
            'pagination' => [
                'pageSizeParam'=>'stream-perpage',
                'pageParam'=>'stream-page',
                'pageSize' => 10,
            ]
      ]);
      return $this->render('view', [
          'teamInstanceProvider' => $teamInstanceProvider,
          'dataProvider' => $dataProvider,
          'streamProvider'=>$streamProvider,
          'team' => Yii::$app->user->identity->team
      ]);
    }
    /**
     * Lists all Team models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Team::find()->byAcademic(Yii::$app->user->identity->academic)->joinWith(['teamPlayers'])->groupBy(['team.id'])->orderBy(['name'=>SORT_ASC]),
            'pagination' => false,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Team model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
      $model=new CreateTeamForm(['scenario' => CreateTeamForm::SCENARIO_CREATE]);
      if($model->load(Yii::$app->request->post()) && $model->create())
      {
        Yii::$app->user->identity->refresh();
        if(Yii::$app->user->identity->teamLeader!==null)
        {
          Yii::$app->db->createCommand("CALL repopulate_team_stream(:tid)")->bindValue(':tid',Yii::$app->user->identity->teamLeader->id)->execute();
          Yii::$app->session->setFlash('success', \Yii::t('app','Your team has been created.'));
          return $this->redirect(['update']);
        }
      }
      return $this->render('create', [
          'model' => $model,
      ]);

    }
    /**
     * Renew a Team token.
     * @return mixed
     */
    public function actionRenew($token=null)
    {
      $team=$this->findModel(Yii::$app->user->identity->teamLeader->id);
      if(\Yii::$app->cache->memcache->get('team_renewed:'.$team->id)!==false)
      {
        \Yii::$app->session->addFlash('warning', \Yii::t('app','Please wait 5 minutes until you can renew your URL again.'));
      }
      else
      {
        $team->updateAttributes(['token'=>Yii::$app->security->generateRandomString(10)]);
        \Yii::$app->cache->memcache->set('team_renewed:'.$team->id,true,360);
        \Yii::$app->session->addFlash('success', \Yii::t('app','Your team invite URL got renewed. You will have to wait 5 minutes before you can renew again.'));
      }
      return $this->redirect(['view','token'=>$team->token]);
    }


    /**
     * Join user to team.
     * If update is successful, the browser will be redirected to the 'view' page.
     */
    public function actionJoin($token)
    {

      $team=$this->findModel(['token'=>$token]);

      if($team->academic!==Yii::$app->user->identity->academic)
      {
        Yii::$app->session->setFlash('error', \Yii::t('app','The team you tried to join was not on the same academic scope.'));
        return $this->redirect(['index']);
      }
      elseif($team->getTeamPlayers()->count()>=intval(Yii::$app->sys->members_per_team))
      {
        Yii::$app->session->setFlash('error', \Yii::t('app','The team you are trying to join is full.'));
        return $this->redirect(['index']);
      }
      elseif($team->locked)
      {
        Yii::$app->session->setFlash('error', \Yii::t('app','The team you tried to join is locked. No new members can join!'));
        return $this->redirect(['index']);
      }

      $tp=new TeamPlayer();
      $tp->player_id=Yii::$app->user->id;
      $tp->team_id=$team->id;
      $tp->approved=0;
      if($tp->save()===false)
      {
        Yii::$app->session->setFlash('error', \Yii::t('app','Failed to join the team, unknown error occurred.'));
        return $this->redirect(['index']);
      }

      $tp->notifyJoinOwner();
      Yii::$app->session->setFlash('success', \Yii::t('app','You joined the team but it is pending approval by the team leader.'));
      return $this->redirect(['view','token'=>$team->token]);

    }

    /**
     * Updates an existing Team model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate()
    {
        $team=$this->findModel(Yii::$app->user->identity->teamLeader->id);
        $team->scenario='update';
        if($team->load(Yii::$app->request->post()) && $team->validate())
        {
            if($team->save())
            {
              $team->uploadedAvatar = UploadedFile::getInstance($team, 'uploadedAvatar');
              $team->saveLogo();
              Yii::$app->session->setFlash('success', \Yii::t('app','Your team was updated.'));
              return $this->redirect(['view','token'=>$team->token]);
            }
        }

        return $this->render('update',['model'=>$team]);
    }

    public function actionApprove($id)
    {
      $tp=$this->findTPModel($id);

      if($tp->team_id!==Yii::$app->user->identity->teamLeader->id)
      {
        Yii::$app->session->setFlash('error', \Yii::t('app','You have no permission to approve this membership.'));
        return $this->redirect(['index']);
      }
      $tp->approved=1;
      if(!$tp->save())
      {
        Yii::$app->session->setFlash('error', \Yii::t('app','Failed to approve membership.'));
        return $this->redirect(['view','token'=>$tp->team->token]);
      }
      Yii::$app->db->createCommand("CALL repopulate_team_stream(:tid)")->bindValue(':tid',$tp->team_id)->execute();
      $tp->notifyApprovePlayer();
      Yii::$app->session->setFlash('success', \Yii::t('app','Membership approved.'));
      return $this->redirect(['view','token'=>$tp->team->token]);

    }

    public function actionReject($id)
    {
      $tp=$this->findTPModel($id);
      $token=$tp->team->token;
      if($tp->player_id!==Yii::$app->user->id && $tp->team_id!==Yii::$app->user->identity->teamLeader->id)
      {
        Yii::$app->session->setFlash('error', \Yii::t('app','You have no permission to cancel this membership.'));
        return $this->redirect(['index']);
      }

      if($tp->delete()===false)
      {
        Yii::$app->session->setFlash('error', \Yii::t('app','Failed to cancel membership.'));
        return $this->redirect(['view','token'=>$token]);
      }
      $redir=['view','token'=>$token];
      if($tp->player_id===Yii::$app->user->id)
      {
        if(Yii::$app->user->identity->teamLeader)
        {
          $this->delete_with_extras();
        }
        $redir=['index'];
        Yii::$app->db->createCommand("CALL repopulate_team_stream(:tid)")->bindValue(':tid',$tp->team_id)->execute();
        Yii::$app->session->setFlash('success', \Yii::t('app','Your membership has been withdrawn.'));
      }
      else {
        Yii::$app->session->setFlash('success', \Yii::t('app','Membership has been withdrawn.'));
      }
      if(Yii::$app->user->identity->teamLeader)
      {
        $tp->notifyRejectPlayer();
      }
      else
      {
        $tp->notifyPartOwner();
      }
      return $this->redirect($redir);
    }

    public function actionInvite($token)
    {
      $team=$this->findModel(['token'=>$token]);

      if($team->academic!==Yii::$app->user->identity->academic)
      {
        Yii::$app->session->setFlash('error', \Yii::t('app','The team you are trying to access is not of the same academic type.'));
        return $this->redirect(['index']);
      }
      elseif($team->getTeamPlayers()->count()>=intval(Yii::$app->sys->members_per_team))
      {
        Yii::$app->session->setFlash('error', \Yii::t('app','The team you are trying to join is full.'));
        return $this->redirect(['index']);
      }
      elseif($team->locked)
      {
        Yii::$app->session->setFlash('error', \Yii::t('app','The team you tried to join is locked. No new members can join!'));
        return $this->redirect(['index']);
      }

      if(Yii::$app->request->isPost)
      {
        $tp=new TeamPlayer();
        $tp->player_id=Yii::$app->user->id;
        $tp->team_id=$team->id;
        $tp->approved=0;
        if($tp->save())
        {
          Yii::$app->session->setFlash('error', \Yii::t('app','Failed to add you as member of this team.'));
          return $this->redirect(['index']);
        }
      }
      return $this->render('invite',['team'=>$team]);
    }

    /**
     * Finds the Team model based on its primary key value or specific
     * condition (eg [token=>'val']).
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer|array $id
     * @return Team the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(($model=Team::findOne($id)) !== null)
        {
            return $model;
        }
        throw new NotFoundHttpException(\Yii::t('app','The requested page does not exist.'));
    }

    /**
     * Finds the TeamPlayer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer|array $id
     * @return TeamPlayer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findTPModel($id)
    {
        if(($model=TeamPlayer::findOne($id)) !== null)
        {
            return $model;
        }
        throw new NotFoundHttpException(\Yii::t('app','The requested page does not exist.'));
    }


    protected function delete_with_extras()
    {
      if(Yii::$app->user->identity->teamLeader->logo!==null)
      {
        $fname=Yii::getAlias(sprintf('@app/web/images/avatars/team/%s.png',Yii::$app->user->identity->teamLeader->id));
        @unlink($fname);
      }
      Yii::$app->user->identity->teamLeader->delete();
    }
}
