<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\modules\target\models\Target;
use app\models\Player;
use app\models\Stream;

class GeneratorController extends Controller {

  /**
   * Generate composite target logo used for social media
   * Take a background image (twnew-target.png) and place on top the target
   * thumbnail logo. Place the target name and purpose on top of the image.
   * Save generated image based on target name.
   */
  public function actionTargetSocialImages($target_id=null)
  {
    if($target_id===null)
      $targets=Target::find()->all();
    else
      $targets=Target::find()->where(['id'=>$target_id])->all();

    $font = \Yii::getAlias("@app/web/fonts/RobotoMono-Regular.ttf");
    foreach($targets as $target)
    {
      try {
        $target_img=imagecreatefrompng(\Yii::getAlias("@app/web/images/targets/_".$target->name."-thumbnail.png"));
        $background_img=imagecreatefrompng(\Yii::getAlias("@app/web/images/twnew-target.png"));
        imagealphablending($target_img, true);
        $width  = imagesx($target_img);
        $height = imagesy($target_img);
        imagesavealpha($background_img, true);
        imagecopy($background_img, $target_img, 930-($width+10),55 , 0, 0, $width, $height);
        $green = imagecolorallocate($background_img, 148,193,31);
        $white = imagecolorallocate($target_img, 255, 255, 255);
        $grey  = imagecolorallocate($target_img, 128, 128, 128);
        $black = imagecolorallocate($target_img, 0, 0, 0);
        $fontsize=40;
        $angle=0;
        $x=60;
        $y=220;

        // Make the target name the text to draw
        $text = $target->name;
        // Draw text shadow
        imagefttext($background_img, $fontsize, $angle, $x+2, ($y+2)-130, $grey, $font, $text);
        // Draw actual text
        imagefttext($background_img, $fontsize, $angle, $x, $y-130, $green, $font, $text);

        // Make the target IP the text to draw
        $text=long2ip($target->ip);
        imagefttext($background_img, $fontsize-5, $angle, $x+2, ($y+2)-75, $grey, $font, $text);
        imagefttext($background_img, $fontsize-5, $angle, $x,   $y-75,     $green, $font, $text);

        $purposes=explode("\n",wordwrap(trim($target->purpose),50));
        foreach($purposes as $key=>$val)
        {
          imagefttext($background_img, 14, $angle, 10+$x+1, 175+($key*20)+1, $black,$font,trim($val));
          imagefttext($background_img, 14, $angle, 10+$x,   175+($key*20),   $green,$font,trim($val));
        }

        imagepng($background_img, \Yii::getAlias("@app/web/images/targets/".$target->name.".png"));
        imagedestroy($target_img);
        imagedestroy($background_img);
      } catch (\Exception $e) {
        echo "Failed generation for ",$target->name, ". Error: ",$e->getMessage(),"\n";
      }
    }
  }
  /**
   * Generate sitemap.xml
   */
    public function actionSitemap($profiles=false, $baseurl=null)
    {
      if($baseurl===null)
      {
        $baseurl=sprintf("https://%s/",\Yii::$app->sys->offense_domain);
      }
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
      public function actionAvatar($active=true,$atomic=true)
      {
        if(!$atomic)
        {
          echo "Starting transaction\n";
          $transaction = \Yii::$app->db->beginTransaction();
        }
        try
        {
          if(boolval($active)===true)
            $players=Player::find()->active()->all();
          else
            $players=Player::find()->all();
          foreach($players as $player)
          {
            $dst_img=\Yii::getAlias('@app/web/images/avatars/'.$player->profile->id.'.png');
            if($player->profile->avatar === 'default.png' || !$player->profile->avatar || !file_exists($dst_img))
            {
              echo "Generating ".$player->username." profile avatar image.\n";
              $robohash=new \app\models\Robohash($player->profile->id,'set1');
              $image=$robohash->generate_image();
              if(get_resource_type($image)=== 'gd')
              {
                imagepng($image,$dst_img);
                imagedestroy($image);
                $player->profile->avatar=$player->profile->id.'.png';
                $player->profile->save(false);
              }
            }
          }
          if(!$atomic)
          {
            echo "Commiting transaction\n";
            $transaction->commit();
          }
        }
        catch (\Exception $e)
        {
          if(!$atomic)
            $transaction->rollBack();
          throw $e;
        } catch (\Throwable $e) {
          if(!$atomic)
            $transaction->rollBack();
          throw $e;
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
