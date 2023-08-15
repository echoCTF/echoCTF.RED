<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\modules\target\models\Target;
use app\models\Player;
use app\models\Stream;

class GeneratorController extends Controller {

  /**
   * Create a local file with the url routes from memcached
   */
  public function actionSysconfig($outfile="sysconfig.php")
  {
    $records=\Yii::$app->db->createCommand('select id,val from sysconfig')->queryAll();
    $line['sysconfig:admin_ids']="\t'sysconfig:admin_ids' => '',";
    $line['sysconfig:player_disabled_routes']="\t'sysconfig:player_disabled_routes' => '',";
    $line['sysconfig:site_description']="\t'sysconfig:site_description' => '',";
    $line['sysconfig:discord_invite']="\t'sysconfig:discord_invite' => '',";
    $line['sysconfig:css_override']="\t'sysconfig:css_override' => null,";
    $line['sysconfig:bannedIPs']="\t'sysconfig:bannedIPs' => '',";

    foreach($records as $rec)
      $line['sysconfig:'.$rec['id']]=sprintf("\t'sysconfig:%s' => \"%s\",",$rec['id'],addslashes($rec['val']));

    printf("<?php\nreturn [\n%s\n];\n",implode("\n",$line));
  }

  /**
   * Create a local file with the url routes from memcached
   */
  public function actionRoutes($outfile="routes.php")
  {
    if(\Yii::$app->sys->routes===false || \Yii::$app->sys->routes===null)
      return;
    try
    {
      $routes[]=['source'=>'', 'destination' => 'site/index'];
      $routes[]=['source'=>'/','destination' => 'site/index'];
      $routes=\yii\helpers\ArrayHelper::merge($routes,\yii\helpers\Json::decode(\Yii::$app->sys->routes));
      foreach($routes as $entry)
      {
        $lines[]=sprintf("  '%s' => '%s',",$entry['source'],$entry['destination']);
      }
      $content=sprintf("<?php\nreturn [\n%s\n];", implode("\n",$lines));
      $dirn=\Yii::getAlias("@app/config");
      file_put_contents("{$dirn}/{$outfile}",$content);
    }
    catch (\Exception $e)
    {
      echo "Failed to generate {$outfile}\n",$e->getMessage(),"\n";
    }
  }

  /**
   * Create a local file with the disabled routes from memcached
   */
  public function actionDisabledRoutes($outfile="disabled-routes.php")
  {
    if(\Yii::$app->sys->disabled_routes===false || \Yii::$app->sys->disabled_routes===null)
      return;
    try
    {
      $routes=\yii\helpers\Json::decode(\Yii::$app->sys->disabled_routes);
      foreach($routes as $entry)
      {
        $lines[]=sprintf("  ['route'=>'%s'],",$entry['route']);
      }
      $content=sprintf("<?php\nreturn [\n%s\n];", implode("\n",$lines));
      $dirn=\Yii::getAlias("@app/config");
      file_put_contents("{$dirn}/{$outfile}",$content);
    }
    catch (\Exception $e)
    {
      echo "Failed to generate {$outfile}\n",$e->getMessage(),"\n";
    }
  }

  /**
   * Create a local file with the player disabled routes from memcached
   */
  public function actionPlayerDisabledRoutes($outfile="player-disabled-routes.php")
  {
    if(\Yii::$app->sys->player_disabled_routes===false || \Yii::$app->sys->player_disabled_routes===null)
      return;

    try
    {
      $routes=\yii\helpers\Json::decode(\Yii::$app->sys->player_disabled_routes);
      foreach($routes as $entry)
      {
        $lines[]=sprintf("  'route'=>'%s',",$entry['route']);
      }
      $content=sprintf("<?php\nreturn [\n%s\n];", implode("\n",$lines));
      $dirn=\Yii::getAlias("@app/config");
      file_put_contents("{$dirn}/{$outfile}",$content);
    }
    catch (\yii\base\InvalidArgumentException $e)
    {
      echo "JSON::decode failure: ".$e->getMessage(),"\n";
    }
    catch (\Exception $e)
    {
      echo "Failed to generate $outfile\n",$e->getMessage(),"\n";
    }
  }

  /**
   * Generate local mail templates from database
   */
  public function actionEmailTemplates($interval=60)
  {
    $models=\app\modelscli\EmailTemplate::find()->last($interval)->all();
    $dirn=\Yii::getAlias("@app/mail");
    foreach($models as $model)
    {
      echo "Generating ".$model->name,"\n";
      $txt=sprintf("%s/%s-text.php",$dirn,$model->name);
      $html=sprintf("%s/%s-html.php",$dirn,$model->name);
      file_put_contents($html,$model->html);
      file_put_contents($txt,$model->txt);
    }
  }

  /**
   * Generate composite target logo used for social media
   * Take a background image (twnew-target.png) and place on top the target
   * thumbnail logo. Place the target name and purpose on top of the image.
   * Save generated image based on target name.
   */
  public function actionTargetSocialImages($target_id=null,$pending=true)
  {
    if($target_id===null || intval($target_id)==0)
      $targets=Target::find()->all();
    else
      $targets=Target::find()->where(['id'=>$target_id])->all();

    $font = \Yii::getAlias("@app/web/fonts/RobotoMono-Regular.ttf");
    foreach($targets as $target)
    {
      if($pending===true && file_exists(\Yii::getAlias("@app/web/images/targets/".$target->name.".png"))===true)
        continue;
      try
      {
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

        $purposes=explode("\n",wordwrap(trim($target->purpose),50));
        foreach($purposes as $key=>$val)
        {
          imagefttext($background_img, 14, $angle, $x+2+$key, ($y+($key*19))-80, $black,$font,trim($val));
          imagefttext($background_img, 14, $angle, $x+2+$key,   ($y+($key*19))-81,   $green,$font,trim($val));
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
        else
        {
          printf("Player [%s] error in badge generation.\n",$player->username);
        }
      }
    }

    public function actionBadges($owner=0, $interval=86400,$limit=200)
    {
      $players=Player::find()->active();
      $processed=0;
      foreach($players->all() as $item)
      {
        $avatarPath=\Yii::getAlias('@app/web/images/avatars').'/'.$item->profile->id.'.png';
        $path=\Yii::getAlias('@app/web/images/avatars/badges/').$item->profile->id.'.png';
        if($processed>intval($limit))
          break;
        if(file_exists($avatarPath) && (file_exists($path) && filemtime($path)<(time()-intval($interval))))
        {
          $processed++;
          echo "Processing user: ",$item->username;
          $image=\app\components\Img::profile($item->profile);
          if($image!==false)
          {
            try {
              imagepng($image,$path);
              chown($path, $owner);
              imagedestroy($image);
              echo " badge generated\n";

            } catch (\Exception $e) {
              echo " failed avatar [",$avatarPath,"] not found\n";
              echo " ",$e->getMessage(),"\n";
            }
          }
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
