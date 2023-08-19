<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * This is the model class for generating and manipulating images.
 *
 */
class Img extends Component
{
      public static function profile($profile)
      {
        try {
          $fname=Yii::getAlias(sprintf('@app/web/images/avatars/%s',$profile->avtr));

          $image = imagecreatetruecolor(800,220);
          if($image===false) throw new \yii\base\UserException(\Yii::t('app',"Error: Source avatar png not found!"));

          imagealphablending($image, false);
          $col=imagecolorallocatealpha($image,255,255,255,127);
          $textcolor = imagecolorallocate($image, 255, 255, 255);
          $consolecolor = imagecolorallocate($image, 148,148,148);
          $greencolor = imagecolorallocate($image, 148,193,31);

          imagefilledrectangle($image,0,0,800, 220,$col);
          imagefilledrectangle($image,20,20,180, 180,$greencolor);
          imagealphablending($image,true);
          $src = imagecreatefrompng($fname);
          if($src===false)
            throw new \yii\base\UserException(\Yii::t('app','Error processing image file!'));
          $x=160;
          $avatar=imagescale($src,$x);
          if($avatar===false)
            throw new \yii\base\UserException(\Yii::t('app','Error in image scale!'));

          imagecopyresampled($image, $avatar, /*dst_x*/ 20, /*dst_y*/ 20, /*src_x*/ 0, /*src_y*/ 0, /*dst_w*/ $x, /*dst_h*/ $x, /*src_w*/ $x, /*src_y*/ $x);
          imagealphablending($image,true);

          $cover = imagecreatefrompng(Yii::getAlias('@app/web/images/badge.tpl.png'));
          if($cover===false)
            throw new \yii\base\UserException(\Yii::t('app','Error processing cover image file!'));

          imagecopyresampled($image, $cover, 0, 0, 0, 0, 800, 220, 800, 220);
          imagealphablending($image,true);

          imagealphablending($image, false);
          imagesavealpha($image, true);

          $lineheight=20;
          $i=1;
          imagestring($image, 6, 200, $lineheight*$i++, sprintf(\Yii::t('app',"root@%s:/# ./userinfo --profile %d"),\Yii::$app->sys->offense_domain,$profile->id),$textcolor);
          imagestring($image, 6, 200, $lineheight*$i++, sprintf(\Yii::t('app',"username.....: %s"),$profile->owner->username),$greencolor);
          imagestring($image, 6, 200, $lineheight*$i++, sprintf(\Yii::t('app',"joined.......: %s"),date("d.m.Y", strtotime($profile->owner->created))),$greencolor);
          imagestring($image, 6, 200, $lineheight*$i++, sprintf(\Yii::t('app',"points.......: %s"),number_format($profile->owner->playerScore->points)),$greencolor);
          imagestring($image, 6, 200, $lineheight*$i++, sprintf(\Yii::t('app',"rank.........: %s"),$profile->owner->playerScore->points == 0 ? "-":$profile->rank->ordinalPlace),$greencolor);
          imagestring($image, 6, 200, $lineheight*$i++, sprintf(\Yii::t('app',"level........: %d / %s"),$profile->experience->id, $profile->experience->name),$greencolor);
          imagestring($image, 6, 200, $lineheight*$i++, sprintf(\Yii::t('app',"flags........: %d"), $profile->totalTreasures),$greencolor);
          imagestring($image, 6, 200, $lineheight*$i++, sprintf(\Yii::t('app',"challenges...: %d / %d first"),$profile->challengesSolverCount, $profile->firstChallengeSolversCount),$greencolor);
          imagestring($image, 6, 200, $lineheight*$i++, sprintf(\Yii::t('app',"headshots....: %d / %d first"),$profile->headshotsCount, $profile->firstHeadshotsCount),$greencolor);
          imagedestroy($avatar);
          imagedestroy($cover);
          imagedestroy($src);
        }
        catch(\Exception $e)
        {
            return false;
        }
        return $image;
      }
}
