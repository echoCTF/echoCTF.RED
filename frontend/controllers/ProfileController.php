<?php

namespace app\controllers;
use Yii;
use app\models\Profile;
use yii\data\ActiveDataProvider;
use \app\modules\target\models\Target;
use \app\modules\target\models\TargetQuery;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;


class ProfileController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['me','index','notifications','hints','update','ovpn','settings'],
                'rules' => [
                    [
                        'actions' => ['me','notifications','hints','update','ovpn','settings'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                    ],
                ],
            ],
            [
              'class' => 'yii\filters\AjaxFilter',
              'only' => ['notifications','hints']
            ],
          'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                ],
            ],
        ];
    }

    public function actions()
    {
      $actions = parent::actions();
      $actions['notifications']['class'] = 'app\actions\profile\NotificationsRestAction';
      $actions['hints']['class'] = 'app\actions\profile\HintsRestAction';
      return $actions;
    }

    public function actionMe()
    {
      $profile=Yii::$app->user->identity->profile;
      $model=\app\models\Stream::find()
        ->select('stream.*,TS_AGO(ts) as ts_ago')
        ->where(['player_id'=>$profile->player_id])
        ->orderBy(['ts'=>SORT_DESC]);

      $streamProvider = new ActiveDataProvider([
            'query' => $model,
            'pagination' => [
                'pageSizeParam'=>'stream-perpage',
                'pageParam'=>'stream-page',
                'pageSize' => 10,
            ]
        ]);

      return $this->render('index',[
          'profile'=>$profile,
          'streamProvider'=>$streamProvider,
      ]);
    }

    public function actionIndex($id)
    {
      if(intval($id)==intval(Yii::$app->user->id))
        return $this->redirect(['/profile/me']);

      $profile=$this->findModel($id);
      if(Yii::$app->user->isGuest && $profile->visibility!='public')
        			return $this->redirect(['/']);

      if($profile->visibility!='public' && $profile->visibility!='ingame')
        			return $this->redirect(['/']);

      $model=\app\models\Stream::find()->select('stream.*,TS_AGO(ts) as ts_ago')
      ->where(['player_id'=>$profile->player_id])
      ->orderBy(['ts'=>SORT_DESC]);
      $streamProvider = new ActiveDataProvider([
          'query' => $model,
          'pagination' => [
              'pageSizeParam'=>'stream-perpage',
              'pageParam'=>'stream-page',
              'pageSize' => 10,
          ]
        ]);
        return $this->render('index',[
          'profile'=>$profile,
          'streamProvider'=>$streamProvider,
          'accountForm'=>null,
          'profileForm'=>null,
        ]);
    }

    public function actionUpdate()
    {
        $profile=$this->findModel(Yii::$app->user->id);

        $errors=$success=null;
        $profileForm=$profile;
        $profileForm->scenario='me';
        die(var_dump(Yii::$app->request->post()));
        if ($profileForm->load(Yii::$app->request->post()) && $profileForm->save())
          $success[]="Profile updated";
        else
          $errors[]='Failed to update profile';

        $accountForm=$profile->owner;
        $accountForm->scenario='profile';
        if ($accountForm->load(Yii::$app->request->post()) && $accountForm->save())
          $success[]="Player updated";
        else
          $errors[]='Failed to update player';

        if($errors!==null)
          Yii::$app->session->setFlash('error',$errors);
        if($success!==null)
          Yii::$app->session->setFlash('success',$errors);

        return $this->render('index',[
          'profile'=>$profile,
          'playerSpin'=>$playerSpin,
          'streamProvider'=>$streamProvider,
          'accountForm'=>$accountForm,
          'profileForm'=>$profileForm,
        ]);
    }

    public function actionOvpn()
  	{
  		$model = Yii::$app->user->identity->sSL;
  		$content=\Yii::$app->view->renderFile('@app/views/profile/ovpn.php',array('model'=>$model),true);
      \Yii::$app->response->data=$content;
      \Yii::$app->response->setDownloadHeaders('echoCTF.ovpn','application/octet-stream',false,strlen($content));
      return Yii::$app->response->send();

  	}

    public function actionSettings()
    {
      $errors=$success=null;

      $profile=Yii::$app->user->identity->profile;
      $profileForm=$profile;
      $profileForm->scenario='me';
      $accountForm=$profile->owner;
      if(Yii::$app->request->isPost)
      {
        if(Yii::$app->request->post('Profile'))
        {
          if ($profileForm->load(Yii::$app->request->post(),'Profile') && $profileForm->update()!==false)
          {
            $success[]="Profile updated";
          }
          else
          {
            $errors[]='Failed to update profile';
          }
        }

        if(Yii::$app->request->post('Player'))
        {
          if(trim(Yii::$app->request->post('Player')['password'])!=="")
          {
            $accountForm->scenario='password_change';
          }
          if ($accountForm->load(Yii::$app->request->post(),'Player')===true)
          {
            $accountForm->setPassword($accountForm->password);
            $accountForm->confirm_password=$accountForm->password;
            if($accountForm->update()!==false)
              $success[]="Player updated";
            else
              $errors[]='Failed to update account';
          }
        }
      }

      if($errors!==null)
        Yii::$app->session->setFlash('error',$errors);
      if($success!==null)
        Yii::$app->session->setFlash('success',$success);

      $accountForm->confirm_password=$accountForm->password=null;
      //die(var_dump(Yii::$app->session->getAllFlashes()));
      $command = Yii::$app->db->createCommand('select * from player_spin WHERE player_id=:player_id');
      $playerSpin=$command->bindValue(':player_id',Yii::$app->user->id)->query()->read();

      return $this->render('settings',[
        'profile'=>$profile,
        'playerSpin'=>$playerSpin,
        'accountForm'=>$accountForm,
        'profileForm'=>$profileForm
      ]);
    }
    /**
     * Finds the Profile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Profile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Profile::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
