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
use yii\web\UploadedFile;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
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
                'only' => ['index', 'create', 'join', 'update', 'approve', 'reject', 'invite'],
                'rules' => [
                  [
                     'actions' => ['reject'],
                     'allow' => false,
                     'matchCallback' => function ($rule, $action) {
                         return Yii::$app->sys->event_start!==false && (time()>=Yii::$app->sys->event_start && time()<=Yii::$app->sys->event_end);
                     },
                     'denyCallback' => function() {
                       \Yii::$app->session->setFlash('info', 'These actions are disabled during the competition');
                       return  \Yii::$app->getResponse()->redirect(['/dashboard/index']);
                     }
                 ],
                 'disabledRoute'=>[
                     'allow' => false,
                     'matchCallback' => function ($rule, $action) {
                       return Yii::$app->DisabledRoute->disabled($action);
                     },
                     'denyCallback' => function() {
                       throw new \yii\web\HttpException(404,'This area is disabled.');
                     },
                 ],
                 [
                   'actions' => ['index', 'create', 'join', 'update', 'approve', 'reject', 'invite'],
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
     * Lists all Team models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(Yii::$app->user->identity->team!==null)
        {

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

        $dataProvider = new ActiveDataProvider([
            'query' => Team::find()->joinWith(['teamPlayers'])->groupBy(['team.id']), //->andFilterHaving(['<', 'count(player_id)', Yii::$app->sys->members_per_team]),
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
      if(Yii::$app->user->identity->teamLeader!==null || Yii::$app->user->identity->team!==null )
      {
        return $this->redirect(['index']);
      }

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
      if(Yii::$app->user->identity->team!==null)
      {
        Yii::$app->session->setFlash('error', 'You are already member of a team.');
        return $this->redirect(['index']);
      }

      $team=Team::findOne(['token'=>$token]);

      if($team===null)
      {
        Yii::$app->session->setFlash('error', 'The token you provided does not belong to any the teams.');
        return $this->redirect(['index']);
      }

      if($team->academic!==Yii::$app->user->identity->academic)
      {
        Yii::$app->session->setFlash('error', 'The team you tried to join was not on the same academic scope.');
        return $this->redirect(['index']);
      }
      if($team->getTeamPlayers()->count()>=intval(Yii::$app->sys->members_per_team))
      {
        Yii::$app->session->setFlash('error', 'The team you are trying to join is full.');
        return $this->redirect(['index']);
      }

      $tp=new TeamPlayer();
      $tp->player_id=Yii::$app->user->id;
      $tp->team_id=$team->id;
      $tp->approved=0;
      if(!$tp->save())
      {
        Yii::$app->session->setFlash('error', 'Failed to join the team, unknown error occurred.');
        return $this->redirect(['index']);
      }
      Yii::$app->session->setFlash('success', 'You joined the team but it is pending approval by the team leader.');
      // XXX Add notification to the team leader
      return $this->redirect(['index']);

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
        if(Yii::$app->user->identity->teamLeader===null)
        {
          Yii::$app->session->setFlash('error', 'You are not the leader of any teams.');
          return $this->redirect(['index']);
        }

        $team=Team::findOne(Yii::$app->user->identity->teamLeader->id);
        $team->scenario='update';

        if($team->load(Yii::$app->request->post()) && $team->validate())
        {
          $team->uploadedAvatar = UploadedFile::getInstance($team, 'uploadedAvatar');
          if($team->uploadedAvatar && $this->HandleUpload($team->uploadedAvatar))
          {
            $fname=Yii::getAlias(sprintf('@app/web/images/avatars/team/%s.png',$team->id));
            $team->logo=sprintf('%s.png',$team->id);
            if($team->save() && $team->uploadedAvatar->saveAs($fname))
            {
              Yii::$app->session->setFlash('success', 'Your team was updated.');
              return $this->redirect(['index']);
            }
            else
            {
              Yii::$app->session->setFlash('error', 'Failed to update your team details.');
            }
          }
          elseif($team->save())
          {
            Yii::$app->session->setFlash('success', 'Your team was updated.');
            return $this->redirect(['index']);
          }
        }
        return $this->render('update',['model'=>$team]);
    }

    public function actionApprove($id)
    {
      $tp=TeamPlayer::findOne($id);
      if($tp===null)
      {
        Yii::$app->session->setFlash('error', 'Could not find requested membership.');
        return $this->redirect(['index']);
      }

      if($tp->team_id!==Yii::$app->user->identity->teamLeader->id)
      {
        Yii::$app->session->setFlash('error', 'You have no permission to approve this membership.');
        return $this->redirect(['index']);
      }
      $tp->approved=1;
      if(!$tp->save())
      {
        Yii::$app->session->setFlash('error', 'Failed to approve membership.');
        return $this->redirect(['index']);
      }
      Yii::$app->db->createCommand("CALL repopulate_team_stream(:tid)")->bindValue(':tid',$tp->team_id)->execute();

      Yii::$app->session->setFlash('success', 'Membership approved.');
      return $this->redirect(['index']);

    }
    public function actionReject($id)
    {
      $tp=TeamPlayer::findOne($id);
      if($tp===null)
      {
        Yii::$app->session->setFlash('error', 'Could not find requested membership.');
        return $this->redirect(['index']);
      }

      if($tp->player_id!==Yii::$app->user->id && $tp->team_id!==Yii::$app->user->identity->teamLeader->id)
      {
        Yii::$app->session->setFlash('error', 'You have no permission to cancel this membership.');
        return $this->redirect(['index']);
      }

      if(!$tp->delete())
      {
        Yii::$app->session->setFlash('error', 'Failed to cancel membership.');
        return $this->redirect(['index']);
      }

      if($tp->player_id===Yii::$app->user->id)
      {
        if(Yii::$app->user->identity->teamLeader)
        {
          $this->delete_with_extras();
        }
        Yii::$app->db->createCommand("CALL repopulate_team_stream(:tid)")->bindValue(':tid',$tp->team_id)->execute();
        Yii::$app->session->setFlash('success', 'Your membership has been withdrawn.');
      }
      else {
        Yii::$app->session->setFlash('success', 'Membership has been withdrawn.');
      }
      return $this->redirect(['index']);
    }

    public function actionInvite($token)
    {
      $team=Team::findOne(['token'=>$token]);

      if($team===null)
      {
        Yii::$app->session->setFlash('error', 'There are no teams with this token.');
        return $this->redirect(['index']);
      }
      if(Yii::$app->user->identity->team)
      {
        Yii::$app->session->setFlash('error', 'You are already member of a team.');
        return $this->redirect(['index']);
      }
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
     * Finds the Team model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
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

    protected function HandleUpload($uploadedAvatar)
    {
      if(!$uploadedAvatar) return false;
      $src = imagecreatefrompng($uploadedAvatar->tempName);
      if($src!==false)
      {

        $old_x = imageSX($src);
        $old_y = imageSY($src);
        list($thumb_w,$thumb_h) = $this->ScaledXY($old_x,$old_y);

        $avatar=imagescale($src,$thumb_w,$thumb_h);

        $image = imagecreatetruecolor(300,300);
        if(!$image) return false;

        imagealphablending($image, false);
        $col=imagecolorallocatealpha($image,255,255,255,127);
        imagefilledrectangle($image,0,0,300, 300,$col);
        imagealphablending($image,true);

        list($dst_x,$dst_y) = $this->DestinationXY($thumb_w,$thumb_h);
        imagecopyresampled($image, $avatar, $dst_x, $dst_y, /*src_x*/ 0, /*src_y*/ 0, /*dst_w*/ $thumb_w, /*dst_h*/ $thumb_h, /*src_w*/ $thumb_w, /*src_y*/ $thumb_h);
        imagesavealpha($image, true);
        imagepng($image,$uploadedAvatar->tempName);
        imagedestroy($image);
        imagedestroy($src);
        imagedestroy($avatar);
        return true;
      }
      return false;
    }

    protected function DestinationXY($x,$y)
    {
      $pos_x = $pos_y = 0;

      if($x<300)
      {
        $pos_x = floor((300-$x)/2);
      }
      if($y<300)
      {
        $pos_y = floor((300-$y)/2);
      }
      return [ $pos_x, $pos_y ];
    }

    protected function ScaledXY($old_x,$old_y)
    {
      $thumb_h = $thumb_w = 300;
      if($old_x > $old_y)
      {
        $thumb_w    =   300;
        $thumb_h    =   $old_y*(300/$old_x);
      }

      if($old_x < $old_y)
      {
        $thumb_w    =   $old_x*(300/$old_y);
        $thumb_h    =   300;
      }

      if($old_x == $old_y)
      {
        $thumb_w    =   300;
        $thumb_h    =   300;
      }
      return [$thumb_w, $thumb_h];
    }
    protected function delete_with_extras()
    {
      if(Yii::$app->user->identity->teamLeader->logo!==null)
      {
        $fname=Yii::getAlias(sprintf('@app/web/images/avatars/team/%s.png',Yii::$app->user->identity->teamLeader->id));
        unlink($fname);
      }
      Yii::$app->user->identity->teamLeader->delete();
    }
}
