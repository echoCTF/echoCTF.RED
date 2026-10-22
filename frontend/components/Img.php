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
      $fname = Yii::getAlias(sprintf('@app/web/images/avatars/%s', $profile->avtr));

      $image = imagecreatetruecolor(800, 220);
      if ($image === false) throw new \yii\base\UserException(\Yii::t('app', "Error: Source avatar png not found!"));

      imagealphablending($image, false);
      $col = imagecolorallocatealpha($image, 255, 255, 255, 127);
      $textcolor = imagecolorallocate($image, 255, 255, 255);
      $consolecolor = imagecolorallocate($image, 148, 148, 148);
      $greencolor = imagecolorallocate($image, 148, 193, 31);

      imagefilledrectangle($image, 0, 0, 800, 220, $col);
      imagefilledrectangle($image, 20, 20, 180, 180, $greencolor);
      imagealphablending($image, true);
      $src = imagecreatefrompng($fname);
      if ($src === false)
        throw new \yii\base\UserException(\Yii::t('app', 'Error processing image file!'));
      $x = 160;
      $avatar = imagescale($src, $x);
      if ($avatar === false)
        throw new \yii\base\UserException(\Yii::t('app', 'Error in image scale!'));

      imagecopyresampled($image, $avatar, /*dst_x*/ 20, /*dst_y*/ 20, /*src_x*/ 0, /*src_y*/ 0, /*dst_w*/ $x, /*dst_h*/ $x, /*src_w*/ $x, /*src_y*/ $x);
      imagealphablending($image, true);

      $cover = imagecreatefrompng(Yii::getAlias('@app/web/images/badge.tpl.png'));
      if ($cover === false)
        throw new \yii\base\UserException(\Yii::t('app', 'Error processing cover image file!'));

      imagecopyresampled($image, $cover, 0, 0, 0, 0, 800, 220, 800, 220);
      imagealphablending($image, true);

      imagealphablending($image, false);
      imagesavealpha($image, true);

      $lineheight = 20;
      $i = 1;
      imagestring($image, 6, 200, $lineheight * $i++, sprintf(\Yii::t('app', "root@%s:/# ./userinfo --profile %d"), \Yii::$app->sys->offense_domain, $profile->id), $textcolor);
      imagestring($image, 6, 200, $lineheight * $i++, sprintf(\Yii::t('app', "username.....: %s"), $profile->owner->username), $greencolor);
      imagestring($image, 6, 200, $lineheight * $i++, sprintf(\Yii::t('app', "joined.......: %s"), date("d.m.Y", strtotime($profile->owner->created))), $greencolor);
      if (\Yii::$app->sys->team_only_leaderboards !== true) {
        imagestring($image, 6, 200, $lineheight * $i++, sprintf(\Yii::t('app', "points.......: %s"), number_format($profile->owner->playerScore->points)), $greencolor);
        imagestring($image, 6, 200, $lineheight * $i++, sprintf(\Yii::t('app', "rank.........: %s"), $profile->owner->playerScore->points == 0 ? "-" : $profile->rank->ordinalPlace), $greencolor);
        imagestring($image, 6, 200, $lineheight * $i++, sprintf(\Yii::t('app', "level........: %d / %s"), $profile->experience->id, $profile->experience->name), $greencolor);
        imagestring($image, 6, 200, $lineheight * $i++, sprintf(\Yii::t('app', "flags........: %d"), $profile->totalTreasures), $greencolor);
        imagestring($image, 6, 200, $lineheight * $i++, sprintf(\Yii::t('app', "challenges...: %d / %d first"), $profile->challengesSolverCount, $profile->firstChallengeSolversCount), $greencolor);
        imagestring($image, 6, 200, $lineheight * $i++, sprintf(\Yii::t('app', "headshots....: %d / %d first"), $profile->headshotsCount, $profile->firstHeadshotsCount), $greencolor);
      } else if ($profile->owner->teamPlayer) {
        imagestring($image, 6, 200, $lineheight * $i++, \Yii::t('app', "team.........: {team}", ['team' => $profile->owner->team->name]), $greencolor);
        imagestring($image, 6, 200, $lineheight * $i++, \Yii::t('app', "team rank....: {rank}", ['rank' => ($profile->owner->team->rank !== null ? $profile->owner->team->rank->ordinalPlace : 'empty')]), $greencolor);
        imagestring($image, 6, 200, $lineheight * $i++, \Yii::t('app', "team points..: {points,plural,=0{0 pts} =1{# pts} other{# pts}}", ['points' => ($profile->owner->team->score !== null ? $profile->owner->team->score->points : 0)]), $greencolor);
        imagestring($image, 6, 200, $lineheight * $i++, \Yii::t('app', "contributed..: {points,plural,=0{0 pts} =1{# pts} other{# pts}}", ['points' => ($profile->owner->teamStreamPoints->points ?? 0)]), $greencolor);
      }
      imagedestroy($avatar);
      imagedestroy($cover);
      imagedestroy($src);
    } catch (\Exception $e) {
      return false;
    }
    return $image;
  }

  static function squareAvatar(string $path): bool {
  {
    list($w, $h) = getimagesize($path);
    if ($w <= 0 || $w > 1900 || $h <= 0 || $h > 1900) return false;
    try {
      $src = imagecreatefrompng($path);
      if ($src !== false) {

        $old_x = imageSX($src);
        $old_y = imageSY($src);
        list($thumb_w, $thumb_h) = self::ScaledXY($old_x, $old_y);

        $avatar = imagescale($src, $thumb_w, $thumb_h);

        $image = imagecreatetruecolor(300, 300);
        if (!$image) return false;

        imagealphablending($image, false);
        $col = imagecolorallocatealpha($image, 255, 255, 255, 127);
        imagefilledrectangle($image, 0, 0, 300, 300, $col);
        imagealphablending($image, true);

        list($dst_x, $dst_y) = self::DestinationXY($thumb_w, $thumb_h);
        imagecopyresampled($image, $avatar, $dst_x, $dst_y, /*src_x*/ 0, /*src_y*/ 0, /*dst_w*/ $thumb_w, /*dst_h*/ $thumb_h, /*src_w*/ $thumb_w, /*src_y*/ $thumb_h);
        imagesavealpha($image, true);
        imagepng($image, $path);
        imagedestroy($image);
        imagedestroy($src);
        imagedestroy($avatar);
        return true;
      }
    } catch (\Exception $e) {
      return false;
    }
    return false;
  }

  static function DestinationXY($x, $y)
  {
    $pos_x = $pos_y = 0;

    if ($x < 300) {
      $pos_x = floor((300 - $x) / 2);
    }
    if ($y < 300) {
      $pos_y = floor((300 - $y) / 2);
    }
    return [$pos_x, $pos_y];
  }

  static function ScaledXY($old_x, $old_y)
  {
    $thumb_h = $thumb_w = 300;
    if ($old_x > $old_y) {
      $thumb_w    =   300;
      $thumb_h    =   max(1, (int) round($old_y * (300 / $old_x)));
    }

    if ($old_x < $old_y) {
      $thumb_w    =   max(1, (int) round($old_x * (300 / $old_y)));
      $thumb_h    =   300;
    }

    if ($old_x == $old_y) {
      $thumb_w    =   300;
      $thumb_h    =   300;
    }
    return [$thumb_w, $thumb_h];
  }
}
