<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\modules\target\models\Target;
use app\models\Player;

class GeneratorController extends Controller {

  /**
   * Generate sitemap.xml
   */
    public function actionSitemap($baseurl='https://echoctf.red/')
    {
      $targets=Target::find()->active()->all();
      $contents=$this->renderFile(\Yii::getAlias('@app/views/sitemap.php'), ['targets'=>$targets, 'BASEURL'=>$baseurl, 'profiles'=>[], 'TvsP'=>[]]);
      file_put_contents(\Yii::getAlias('@app/web/sitemap.xml'), $contents);
    }

    /**
     * Generate participant avatars based on robohash
     */
      public function actionAvatar()
      {
        $players=Player::find()->active()->all();
        foreach($players as $player)
        {
          $robohash=new \app\models\Robohash($player->profile->id,'set1');
          $image=$robohash->generate_image();
          if(get_resource_type($image)=== 'gd')
          {
            $dst_img=\Yii::getAlias('@app/web/images/avatars/'.$player->profile->id.'.png');
            imagepng($image,$dst_img);
            imagedestroy($image);
            $player->profile->avatar=$player->profile->id.'.png';
            $player->profile->save(false);
          }
        }
      }

    /**
    * Generate participant avatars based on robohash
    */
    public function actionAuthKeys()
    {
      $players=Player::find()->where(['auth_key'=>''])->active()->all();
      foreach($players as $player)
      {
        $player->generateAuthKey();
        if(!$player->save(false))
        {
          echo $player->id, " ", $player->username, implode(', ', $player->getErrors());
        }
      }
    }

    public function actionBadges($owner=0)
    {
      $players=Player::find()->active()->all();
      foreach($players as $player)
      {
        $image=\app\components\Img::profile($player->profile);
        $path=\Yii::getAlias('@app/web/images/avatars/badges/').'/'.$player->profile->id.'.png';
        imagepng($image,$path);
        chown($path, $owner);
        imagedestroy($image);
      }
    }

    public function actionUrls($domain)
    {
      $config=include(__DIR__.'/../config/web.php');

      $urlmgr=new \yii\web\UrlManager();
      $urlmgr->baseUrl="";
      $urlmgr->setHostInfo($domain);
      $urlmgr->enablePrettyUrl = true;
      $urlmgr->enableStrictParsing = true;
      $urlmgr->showScriptName = false;
      $urlmgr->addRules($config['components']['urlManager']['rules']);
      $urlmgr->init();
      $urllist=[];
      foreach ($config['components']['urlManager']['rules'] as $key => $val)
      {
        if (strstr($key,'<profile') !== false)
        {
          $urllist[]=$urlmgr->createAbsoluteUrl([$val, 'id'=>2, 'profile_id'=>1]);
        }
        elseif (strstr($key,'<id') !== false)
        {
          $urllist[]=$urlmgr->createAbsoluteUrl([$val, 'id'=>1]);
        }
        elseif (strstr($key,'<token') !== false)
        {
          $urllist[]=$urlmgr->createAbsoluteUrl([$val, 'token'=>'abcdedf']);
        }
        else
        {
          $urllist[]=$urlmgr->createAbsoluteUrl($val);
        }
      }
      echo implode("\n",array_unique($urllist)),"\n";
    }
}
