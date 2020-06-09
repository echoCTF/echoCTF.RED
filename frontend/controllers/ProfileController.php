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
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\base\InvalidArgumentException;
use yii\web\UploadedFile;

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
                        'actions' => ['me', 'notifications', 'hints', 'update', 'ovpn', 'settings', 'robohash'],
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
/*
    robohash action will be needed in the future
    public function actionRobohash()
    {
      $robohash=new \app\models\Robohash(Yii::$app->user->identity->username);
      $image=$robohash->generate_image();
      if(get_resource_type($image)=== 'gd')
      {
        Yii::$app->getResponse()->getHeaders()
            ->set('Pragma', 'public')
            ->set('Expires', '0')
            ->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->set('Content-Transfer-Encoding', 'binary')
            ->set('Content-type', 'image/png');

        Yii::$app->response->format = Response::FORMAT_RAW;
        ob_start();
        imagepng($image);
        imagedestroy($image);
        return ob_get_clean();
      }
      // If we reach this point then something went wrong...
      throw new \yii\web\HttpException(500, 'Something went wrong in robohash generation.');
    }
  */

    /**
    * Generate and display profile badge with dynamic details
    */
    public function actionBadge(int $id)
    {
      $profile=$this->findModel($id);
      if(!$profile->visible)
          return $this->redirect(['/']);

      $fname=Yii::getAlias(sprintf('@app/web/images/avatars/%s',$profile->avtr));

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

      Yii::$app->getResponse()->getHeaders()
          ->set('Pragma', 'public')
          ->set('Expires', '0')
          ->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
          ->set('Content-Transfer-Encoding', 'binary')
          ->set('Content-type', 'image/png');

      Yii::$app->response->format = Response::FORMAT_RAW;
      ob_start();
      imagepng($image);
      imagedestroy($image);
      imagedestroy($avatar);
      imagedestroy($cover);
      imagedestroy($src);
      return ob_get_clean();
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
        $profileForm->uploadedAvatar = UploadedFile::getInstance($profileForm, 'uploadedAvatar');
        if($this->HandleUpload($profileForm->uploadedAvatar))
        {
          $fname=Yii::getAlias(sprintf('@app/web/images/avatars/%s',$profileForm->avatar));
          $profileForm->uploadedAvatar->saveAs($fname);
          $profileForm->uploadedAvatar=null;
          $profileForm->approved_avatar=false;
        }
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

      $accountForm->new_password=$accountForm->confirm_password=null;
      return $this->render('settings', [
        'profile'=>$profile,
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

    protected function HandleUpload($uploadedAvatar)
    {
      if(!$uploadedAvatar) return false;
      $src = imagecreatefrompng($uploadedAvatar->tempName);
      if($src!==false)
      {
        $old_x = imageSX($src);
        $old_y = imageSY($src);
        list($thumb_w,$thumb_h) = $this->ScaleXY($old_x,$old_y);

        $avatar=imagescale($src,$thumb_w,$thumb_h);
        imagealphablending($avatar, false);
        imagesavealpha($avatar, true);
        imagepng($avatar,$uploadedAvatar->tempName);
        imagedestroy($src);
        imagedestroy($avatar);
        return true;
      }
      return false;
    }

    protected function ScaleXY($old_x,$old_y)
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
}
