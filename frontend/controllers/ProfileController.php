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

class ProfileController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
            'access' => [
                'class' => AccessControl::class,
                'only' => ['badge','me', 'index', 'notifications', 'hints', 'update', 'ovpn', 'settings'],
                'rules' => [
                   'eventActive'=>[
                      'actions' => ['badge','index', 'notifications', 'hints', 'update', 'ovpn', 'settings'],
                   ],
                   'eventStartEnd'=>[
                     'actions' => ['ovpn'],
                   ],
                   'teamsAccess'=>[
                      'actions' => ['ovpn'],
                   ],
                   'disabledRoute'=>[
                     'actions' => ['badge', 'me', 'notifications', 'hints', 'update', 'ovpn', 'settings'],
                   ],
                   [
                     'actions' => ['index','badge'],
                     'allow' => true,
                   ],
                   [
                     'actions' => ['ovpn','me','settings','notifications','hints'],
                     'allow' => true,
                     'roles'=>['@']
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
        ]);
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
          'me'=>true,
      ]);
    }

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
          'me'=>false,
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
      $settingsForm=new \app\models\forms\SettingsForm();
      $profile=Yii::$app->user->identity->profile;

      if($settingsForm->load(Yii::$app->request->post()) && $settingsForm->validate())
      {
        $settingsForm->uploadedAvatar = UploadedFile::getInstance($settingsForm, 'uploadedAvatar');
        if($this->HandleUpload($settingsForm->uploadedAvatar))
        {
          $fname=Yii::getAlias(sprintf('@app/web/images/avatars/%s.png',$profile->id));
          $settingsForm->avatar=sprintf("%s.png",$profile->id);
          $settingsForm->uploadedAvatar->saveAs($fname);
          $settingsForm->uploadedAvatar=null;
        }
        $settingsForm->save();
        $settingsForm->reset();
      }

      return $this->render('settings', [
        'profile'=>$profile,
        'settingsForm'=>$settingsForm,
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
      $model=Profile::findOne($id);
      if($model === null || $model->owner->active!==1)
      {
        throw new NotFoundHttpException('The requested page does not exist.');
      }

      return $model;
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
}
