<?php

namespace app\controllers;
use Yii;
use app\models\Profile;
use yii\data\ActiveDataProvider;
use \app\modules\target\models\Target;
use \app\modules\game\models\Headshot;
use \app\modules\target\models\TargetQuery;
use yii\helpers\ArrayHelper;
use app\overloads\yii\filters\AccessControl;
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
                'only' => ['badge', 'index', 'invite', 'me', 'ovpn', 'revoke', 'settings', 'notifications', 'hints'],
                'rules' => [
                  'eventActive'=>[
                    'actions' => ['badge','index', 'notifications', 'hints', 'ovpn', 'settings','invite','revoke'],
                  ],
                  'eventStartEnd'=>[
                    'actions' => [''],
                  ],
                  'eventStart'=>[
                    'actions' => ['ovpn','revoke'],
                  ],
                  'eventEnd'=>[
                    'actions' => ['ovpn','revoke'],
                  ],
                  'teamsAccess'=>[
                      'actions' => ['ovpn'],
                  ],
                  'disabledRoute'=>[
                    'actions' => ['badge', 'me', 'index', 'notifications', 'hints', 'ovpn', 'settings', 'invite', 'revoke'],
                  ],
                   [
                     'actions' => ['index','badge'],
                     'allow' => true,
                   ],
                   [
                     'actions' => ['ovpn','me','settings','notifications','hints','revoke'],
                     'allow' => true,
                     'roles'=>['@']
                   ],
                   [
                     'actions' => ['invite'],
                     'allow' => true,
                     'roles'=>['?'],
                   ],
                   [
                     'actions' => ['invite'],
                     'allow' => false,
                     'roles'=>['@'],
                     'denyCallback' => function () {
                       Yii::$app->session->setFlash('info', \Yii::t('app','This area is for guests only!'));
                       return  \Yii::$app->getResponse()->redirect([Yii::$app->sys->default_homepage]);
                     }
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
                'revoke' => ['POST'],
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
      $headshots=Headshot::find()->where(['player_id'=>$profile->player_id])->orderBy(['created_at'=>SORT_ASC]);
      $dataProvider = new ActiveDataProvider([
                 'query' => $headshots,
                 'pagination' => [
                     'pageSize' => 12,
                 ],
                 'sort'=>[
                   'defaultOrder'=>['created_at'=>SORT_ASC]
                 ]
             ]);

      return $this->render('index', [
          'profile'=>$profile,
          'headshots'=>$dataProvider,
          'me'=>true,
      ]);
    }

    /**
    * Generate and display profile badge with dynamic details
    */
    public function actionBadge(int $id)
    {
      $profile=$this->findModel($id);
      if($profile->visible===false)
      {
        Yii::$app->session->setFlash('warning',\Yii::t('app','This profile is not accessible by you.'));
        return $this->redirect(['/']);
      }


      Yii::$app->getResponse()->getHeaders()
          ->set('Pragma', 'public')
          ->set('Expires', '0')
          ->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
          ->set('Content-Transfer-Encoding', 'binary')
          ->set('Content-type', 'image/png');

      Yii::$app->response->format = Response::FORMAT_RAW;
      $playerBadgeFname=\Yii::getAlias('@app/web/images/avatars/badges/').'/'.$profile->id.'.png';
      if(file_exists($playerBadgeFname) && filemtime($playerBadgeFname)>(time()-86400))
      {
        return file_get_contents($playerBadgeFname);
      }
      // Clear file cache since we are about to generate the file
      clearstatcache(true,$playerBadgeFname);

      try {
        $image=\app\components\Img::profile($profile);

        if($image==false)
          return $this->redirect(['/']);

        ob_start();
        imagepng($image);
        imagepng($image,$playerBadgeFname);
        imagedestroy($image);
        return ob_get_clean();
      } catch (\Exception $e) {
        return $this->redirect(['/']);
      }
    }

    public function actionIndex(int $id)
    {
        if(!Yii::$app->user->isGuest && intval($id) == intval(Yii::$app->user->identity->profile->id))
          return $this->redirect(['/profile/me']);

        $profile=$this->findModel($id);
        if(!$profile->visible)
          return $this->redirect(['/']);
        $headshots=Headshot::find()->where(['player_id'=>$profile->player_id])->orderBy(['created_at'=>SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
                   'query' => $headshots,
                   'pagination' => [
                       'pageSize' => 12,
                   ],
                   'sort'=>[
                     'defaultOrder'=>['created_at'=>SORT_ASC]
                   ]
               ]);
        return $this->render('index', [
          'me'=>false,
          'profile'=>$profile,
          'headshots'=>$dataProvider,
          'accountForm'=>null,
          'profileForm'=>null,
        ]);
    }

    public function actionInvite(int $id)
    {
        $profile=$this->findModel($id);
        if(!$profile)
          return $this->redirect(['/']);

        Yii::$app->getSession()->set('referred_by', $profile->owner->id);
        return $this->redirect(['/register']);
    }

    /**
     * Revoke a VPN certificate
     */
    public function actionRevoke()
    {
      if(($model=Yii::$app->user->identity->sSL)===null)
      {
        \Yii::$app->session->addFlash('warning',\Yii::t('app',"No VPN file(s) exist for your profile."));
        return $this->redirect(['/profile/me']);
      }
      try {
        $model->generate();
        $model->save();
        \Yii::$app->session->addFlash('success',\Yii::t('app',"Your keys have been revoked. Make sure you download your OpenVPN configuration file again."));
      } catch (\Exception $e) {
        \Yii::$app->session->addFlash('error',\Yii::t('app',"Failed to revoke your keys."));
      }
      return $this->redirect(['/profile/me']);
    }

    public function actionOvpn($id)
    {

      if(($model=Yii::$app->user->identity->sSL)===null)
      {
        \Yii::$app->session->addFlash('warning',\Yii::t('app',"No VPN file(s) exist for your profile."));
        return $this->redirect(['/profile/me']);
      }
      $template=\app\modelscli\VpnTemplate::findOne(['name'=>$id,'active'=>true,'visible'=>true,'client'=>true]);
      if($template!==null)
      {
        $content=Yii::$app->view->renderPhpContent("?>".$template->content,['model'=>$model]);
        \Yii::$app->response->format=\yii\web\Response::FORMAT_RAW;
        \Yii::$app->response->content=$content;
        \Yii::$app->response->setDownloadHeaders($template->filename, 'application/octet-stream', false, strlen($content));
        return \Yii::$app->response->send();
      }
      \Yii::$app->session->addFlash('error',\Yii::t('app',"No such OpenVPN profile exists."));
      return $this->redirect(['/profile/me']);

    }

    public function actionSettings()
    {

      $settingsForm=new \app\models\forms\SettingsForm();
      $profile=Yii::$app->user->identity->profile;

      if($settingsForm->load(Yii::$app->request->post()) && $settingsForm->validate())
      {
        if($settingsForm->_cf('avatar'))
        {
          $settingsForm->uploadedAvatar = UploadedFile::getInstance($settingsForm, 'uploadedAvatar');
          if($this->HandleUpload($settingsForm->uploadedAvatar))
          {
            $fname=Yii::getAlias(sprintf('@app/web/images/avatars/%s.png',$profile->id));
            $settingsForm->avatar=sprintf("%s.png",$profile->id);
            $settingsForm->uploadedAvatar->saveAs($fname);
            $settingsForm->uploadedAvatar=null;
          }
        }
        $profile->genBadge();
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
        throw new NotFoundHttpException(\Yii::t('app','The requested page does not exist.'));
      }

      return $model;
    }

    protected function HandleUpload($uploadedAvatar)
    {
      if(!$uploadedAvatar) return false;
      try {
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
      }
      catch(\Exception $e)
      {
        return false;
      }
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
