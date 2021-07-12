<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\modules\target\models\Target;
use app\models\Player;
use app\models\Stream;

class GeneratorController extends Controller {

  /**
   * Generate sitemap.xml
   */
    public function actionSitemap($profiles=false, $baseurl='https://echoctf.red/')
    {
      $targets=Target::find()->active()->all();
      if($profiles!==false)
        $players=Player::find()->active()->all();
      else
        $players=[];

      $contents=$this->renderFile(\Yii::getAlias('@app/views/sitemap.php'), ['targets'=>$targets, 'BASEURL'=>$baseurl, 'players'=>$players, 'TvsP'=>[]]);
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

    public function actionAllBadges($owner=0)
    {
      $players=Player::find()->active()->all();
      foreach($players as $player)
      {
        $image=\app\components\Img::profile($player->profile);
        if($image!==false)
        {
          $path=\Yii::getAlias('@app/web/images/avatars/badges/').'/'.$player->profile->id.'.png';
          imagepng($image,$path);
          chown($path, $owner);
          imagedestroy($image);
        }
      }
    }

    public function actionBadges($owner=0, $interval=1440)
    {
      $streamPlayers=Stream::find()->select(['player_id'])->distinct()->where(['>=','ts',new \yii\db\Expression('NOW() - INTERVAL '.$interval.' MINUTE')]);
      foreach($streamPlayers->all() as $item)
      {
        echo "Processing user: ",$item->player->username;
        $avatarPath=\Yii::getAlias('@app/web/images/avatars').'/'.$item->player->profile->id.'.png';
        if(file_exists($avatarPath))
        {
          $image=\app\components\Img::profile($item->player->profile);
          if($image!==false)
          {
            $path=\Yii::getAlias('@app/web/images/avatars/badges/').$item->player->profile->id.'.png';
            imagepng($image,$path);
            chown($path, $owner);
            imagedestroy($image);
            echo " badge generated\n";
          }
        }
        else
        {
          echo " failed avatar [",$avatarPath,"] not found\n";
        }
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
