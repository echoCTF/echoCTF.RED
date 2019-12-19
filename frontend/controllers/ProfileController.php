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
      return $this->render('index',[
          'profile'=>$profile,
      ]);
    }

    public function actionIndex(int $id)
    {
      if(intval($id)==intval(Yii::$app->user->id))
        return $this->redirect(['/profile/me']);

      $profile=$this->findModel($id);
      if(Yii::$app->user->isGuest && $profile->visibility!='public')
        			return $this->redirect(['/']);

      if($profile->visibility!='public' && $profile->visibility!='ingame' && !Yii::$app->user->isGuest && !Yii::$app->user->identity->isAdmin)
        			return $this->redirect(['/']);

        return $this->render('index',[
          'profile'=>$profile,
          'accountForm'=>null,
          'profileForm'=>null,
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
      if ($profileForm->load(Yii::$app->request->post()) && $profileForm->validate()) {
        $profileForm->save();
        $success[]="Profile updated";
      }


      if ($accountForm->load(Yii::$app->request->post()) && $accountForm->validate()) {
        if($accountForm->new_password!="")
        {
          $accountForm->setPassword($accountForm->new_password);
        }
        $accountForm->save();
        $success[]="Account updated";
      }

      if($errors!==null)
        Yii::$app->session->setFlash('error',$errors);
      if($success!==null)
        Yii::$app->session->setFlash('success',$success);

      //die(var_dump(Yii::$app->session->getAllFlashes()));
      $command = Yii::$app->db->createCommand('select * from player_spin WHERE player_id=:player_id');
      $playerSpin=$command->bindValue(':player_id',Yii::$app->user->id)->query()->read();
      $accountForm->new_password=$accountForm->confirm_password=null;
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
