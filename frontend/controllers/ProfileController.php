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
use yii\web\NotFoundHttpException;

class ProfileController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['me', 'index', 'notifications', 'hints', 'update', 'ovpn', 'settings'],
                'rules' => [
                    [
                        'actions' => ['me', 'notifications', 'hints', 'update', 'ovpn', 'settings'],
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
              'only' => ['notifications', 'hints']
            ],
          'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                ],
            ],
        ];
    }

    public function actions()
    {
      $actions=parent::actions();
      $actions['notifications']['class']='app\actions\profile\NotificationsRestAction';
      $actions['hints']['class']='app\actions\profile\HintsRestAction';
      return $actions;
    }

    public function actionMe()
    {
      $profile=Yii::$app->user->identity->profile;
      return $this->render('index', [
          'profile'=>$profile,
      ]);
    }

    /**
    * Generate and display target badge with dynamic details
    */
    public function actionBadge(int $id)
    {
      ob_end_clean();
      $profile=$this->findModel($id);
      if(!$profile->visible)
          return $this->redirect(['/']);

      $fname=Yii::getAlias(sprintf('@app/web/images/avatars/%s',$profile->avatar));

      $image = imagecreatetruecolor(800,220);
      if($image===false) return $this->redirect(['/']);

      imagealphablending($image, false);
      $col=imagecolorallocatealpha($image,255,255,255,127);
      $textcolor = imagecolorallocate($image, 255, 255, 255);
      $consolecolor = imagecolorallocate($image, 148,148,148);
      $greencolor = imagecolorallocate($image, 148,193,31);

      imagefilledrectangle($image,0,0,800, 220,$col);
      imagefilledrectangle($image,20,20,180, 180,$greencolor);
      imagealphablending($image,true);
      header('Content-Type: image/png');

      $src = imagecreatefrompng($fname);
      if($src===false) return $this->redirect(['/']);

      $x=160;
      $avatar=imagescale($src,$x);
      if($avatar===false) return $this->redirect(['/']);

      imagecopyresampled($image, $avatar, /*dst_x*/ 20, /*dst_y*/ 20, /*src_x*/ 0, /*src_y*/ 0, /*dst_w*/ $x, /*dst_h*/ $x, /*src_w*/ $x, /*src_y*/ $x);
      imagealphablending($image,true);

      $cover = imagecreatefrompng(Yii::getAlias('@app/web/images/badge.tpl.png'));
      if($cover===false) return $this->redirect(['/']);

      imagecopyresampled($image, $cover, 0, 0, 0, 0, 800, 220, 800, 220);
      imagealphablending($image,true);


      imagealphablending($image, false);
      imagesavealpha($image, true);

      $lineheight=20;
      $i=1;
      imagestring($image, 6, 200, $lineheight*$i++, sprintf("root@echoctf.red:/# ./userinfo --profile %d",$profile->id),$textcolor);
      imagestring($image, 6, 200, $lineheight*$i++, sprintf("username.....: %s",$profile->owner->username),$greencolor);
      imagestring($image, 6, 200, $lineheight*$i++, sprintf("joined.......: %s",date("d.m.Y", strtotime($profile->owner->created))),$greencolor);
      imagestring($image, 6, 200, $lineheight*$i++, sprintf("points.......: %s",number_format($profile->owner->playerScore->points)),$greencolor);
      imagestring($image, 6, 200, $lineheight*$i++, sprintf("rank.........: %s",$profile->rank->ordinalPlace),$greencolor);
      imagestring($image, 6, 200, $lineheight*$i++, sprintf("level........: %d / %s",$profile->experience->id, $profile->experience->name),$greencolor);
      imagestring($image, 6, 200, $lineheight*$i++, sprintf("flags........: %d", $profile->totalTreasures),$greencolor);
      imagestring($image, 6, 200, $lineheight*$i++, sprintf("headshots....: %d",$profile->headshotsCount),$greencolor);
//      $hs=\app\modules\game\models\Headshot::find()->player_avg_time($profile->player_id)->one();
//      if($hs && $hs->average > 0)
//        imagestring($image, 6, 200, $lineheight*$i++, sprintf("avg headshot.: %s",\Yii::$app->formatter->asDuration($hs->average)),$greencolor);


      ob_get_clean();
      header('Content-Type: image/png');
      imagepng($image);
      imagedestroy($avatar);
      imagedestroy($cover);
      imagedestroy($src);
      imagedestroy($image);
      return;
    }

    public function actionIndex(int $id)
    {
      if(intval($id) == intval(Yii::$app->user->id))
        return $this->redirect(['/profile/me']);

      $profile=$this->findModel($id);
      if(Yii::$app->user->isGuest && $profile->visibility != 'public')
              return $this->redirect(['/']);

      if($profile->visibility != 'public' && $profile->visibility != 'ingame' && !Yii::$app->user->isGuest && !Yii::$app->user->identity->isAdmin)
              return $this->redirect(['/']);

        return $this->render('index', [
          'profile'=>$profile,
          'accountForm'=>null,
          'profileForm'=>null,
        ]);
    }

    public function actionOvpn()
    {
      $model=Yii::$app->user->identity->sSL;
      $content=\Yii::$app->view->renderFile('@app/views/profile/ovpn.php', ['model'=>$model]);
      \Yii::$app->response->format=\yii\web\Response::FORMAT_RAW;
      \Yii::$app->response->content=$content;
      \Yii::$app->response->setDownloadHeaders('echoCTF.ovpn', 'application/octet-stream', false, strlen($content));
      \Yii::$app->response->send();
      return;

    }

    public function actionSettings()
    {
      $errors=[];
      $success=[];

      $profile=Yii::$app->user->identity->profile;
      $profileForm=$profile;
      $profileForm->scenario='me';
      $accountForm=$profile->owner;
      if($profileForm->load(Yii::$app->request->post()) && $profileForm->validate())
      {
        $profileForm->save();
        $success[]="Profile updated";
      }


      if($accountForm->load(Yii::$app->request->post()) && $accountForm->validate())
      {
        if($accountForm->new_password != "")
        {
          $accountForm->setPassword($accountForm->new_password);
        }
        if($accountForm->save())
          $success[]="Account updated";
        else
        {
          $errors[]="Failed to save updated account details.";
        }
      }

      if(!empty($errors))
        Yii::$app->session->setFlash('error', $errors);
      if(!empty($success))
        Yii::$app->session->setFlash('success', $success);

      $command=Yii::$app->db->createCommand('select * from player_spin WHERE player_id=:player_id');
      $playerSpin=$command->bindValue(':player_id', Yii::$app->user->id)->query()->read();
      $accountForm->new_password=$accountForm->confirm_password=null;
      return $this->render('settings', [
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
        if(($model=Profile::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
