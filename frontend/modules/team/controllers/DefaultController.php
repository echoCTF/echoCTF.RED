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
class DefaultController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
          'access' => [
                'class' => \yii\filters\AccessControl::class,
                'only' => ['index', 'create', 'join', 'update', 'approve', 'reject', 'invite', 'mine','view'],
                'rules' => [
                  [
                    'actions'=>['create', 'join', 'update', 'approve', 'reject'],
                    'allow'=> false,
                    'roles' => ['@'],
                    'matchCallback' => function ($rule, $action) {
                      return (\Yii::$app->sys->{"team_manage_members"}===false);
                    },
                    'denyCallback' => function() {
                      \Yii::$app->session->addFlash('error', 'Team management is disabled at the moment.');
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
                      \Yii::$app->session->setFlash('error', 'You are already a member of a team.');
                      return $this->redirect(['mine']);
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
                      \Yii::$app->session->setFlash('error', 'You are already a member of a team.');
                      return $this->redirect(['mine']);
                    }
                  ],
                  [ // Only allow updates from teamLeaders
                    'actions'=> ['update'],
                    'allow' => false,
                    'roles' => ['@'],
                    'matchCallback' => function ($rule, $action) {
                      return \Yii::$app->user->identity->teamLeader===null;
                    },
                    'denyCallback' => function () {
                      \Yii::$app->session->setFlash('error', 'You are not the leader of any teams.');
                      return $this->redirect(['index']);
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
                       \Yii::$app->session->setFlash('info', 'These actions are disabled during the competition');
                       return  \Yii::$app->getResponse()->redirect(['/target/default/index']);
                     }
                 ],
                 'disabledRoute'=>[
                     'allow' => false,
                     'matchCallback' => function ($rule, $action) {
                       return \Yii::$app->DisabledRoute->disabled($action);
                     },
                     'denyCallback' => function () {
                       throw new \yii\web\HttpException(404,'This area is disabled.');
                     },
                 ],
                 [
                   'actions' => ['index', 'mine', 'create', 'join', 'update', 'approve', 'reject', 'invite', 'view'],
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
                ],
            ],
        ];
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
      if(Yii::$app->user->identity->team===null)
      {
        $this->redirect(['index']);
      }
      $dataProvider = new ActiveDataProvider([
          'query' => TeamPlayer::find()->where(['team_id'=>Yii::$app->user->identity->team->id])->orderBy(['ts' => SORT_ASC]),
          'sort' =>false,
          'pagination' => false,
      ]);
      $teamPlayers = ArrayHelper::getColumn(Yii::$app->user->identity->team->players,'id');

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
      return $this->render('view', [
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
            'query' => Team::find()->joinWith(['teamPlayers'])->groupBy(['team.id'])->orderBy(['name'=>SORT_ASC]), 
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
        Yii::$app->db->createCommand("CALL repopulate_team_stream(:tid)")->bindValue(':tid',Yii::$app->user->identity->teamLeader->id)->execute();
        Yii::$app->session->setFlash('success', 'Your team has been created.');
        return $this->redirect(['update']);
      }
      return $this->render('create', [
          'model' => $model,
      ]);

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
        Yii::$app->session->setFlash('error', 'The team you tried to join was not on the same academic scope.');
        return $this->redirect(['view','token'=>$team->token]);
      }

      if($team->getTeamPlayers()->count()>=intval(Yii::$app->sys->members_per_team))
      {
        Yii::$app->session->setFlash('error', 'The team you are trying to join is full.');
        return $this->redirect(['view','token'=>$team->token]);
      }

      $tp=new TeamPlayer();
      $tp->player_id=Yii::$app->user->id;
      $tp->team_id=$team->id;
      $tp->approved=0;
      if($tp->save()===false)
      {
        Yii::$app->session->setFlash('error', 'Failed to join the team, unknown error occurred.');
        return $this->redirect(['view','token'=>$team->token]);
      }

      Yii::$app->session->setFlash('success', 'You joined the team but it is pending approval by the team leader.');
      // XXX Add notification to the team leader
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
              Yii::$app->session->setFlash('success', 'Your team was updated.');
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
        Yii::$app->session->setFlash('error', 'You have no permission to approve this membership.');
        return $this->redirect(['index']);
      }
      $tp->approved=1;
      if(!$tp->save())
      {
        Yii::$app->session->setFlash('error', 'Failed to approve membership.');
        return $this->redirect(['view','token'=>$tp->team->token]);
      }
      Yii::$app->db->createCommand("CALL repopulate_team_stream(:tid)")->bindValue(':tid',$tp->team_id)->execute();

      Yii::$app->session->setFlash('success', 'Membership approved.');
      return $this->redirect(['view','token'=>$tp->team->token]);

    }

    public function actionReject($id)
    {
      $tp=$this->findTPModel($id);
      $token=$tp->team->token;
      if($tp->player_id!==Yii::$app->user->id && $tp->team_id!==Yii::$app->user->identity->teamLeader->id)
      {
        Yii::$app->session->setFlash('error', 'You have no permission to cancel this membership.');
        return $this->redirect(['view','token'=>$token]);
      }

      if($tp->delete()===false)
      {
        Yii::$app->session->setFlash('error', 'Failed to cancel membership.');
        return $this->redirect(['view','token'=>$token]);
      }
      $redir=['view','token'=>$token];
      if($tp->player_id===Yii::$app->user->id)
      {
        if(Yii::$app->user->identity->teamLeader)
        {
          $this->delete_with_extras();
          $redir=['index'];
        }
        Yii::$app->db->createCommand("CALL repopulate_team_stream(:tid)")->bindValue(':tid',$tp->team_id)->execute();
        Yii::$app->session->setFlash('success', 'Your membership has been withdrawn.');
      }
      else {
        Yii::$app->session->setFlash('success', 'Membership has been withdrawn.');
      }
      return $this->redirect($redir);
    }

    public function actionInvite($token)
    {
      $team=$this->findModel(['token'=>$token]);

      if($team->academic!==Yii::$app->user->identity->academic)
      {
        Yii::$app->session->setFlash('error', 'The team you are trying to access is not of the same academic type.');
        return $this->redirect(['index']);
      }

      if($team->getTeamPlayers()->count()>=intval(Yii::$app->sys->members_per_team))
      {
        Yii::$app->session->setFlash('error', 'The team you are trying to join is full.');
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
          Yii::$app->session->setFlash('error', 'Failed to add you as member of this team.');
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
        throw new NotFoundHttpException('The requested page does not exist.');
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
        throw new NotFoundHttpException('The requested page does not exist.');
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
