<?php
// Original Class taken from php-robohash
// (C) 2012 hush2 <hushywushy@gmail.com>
// https://github.com/hush2/php-robohash.git

// Extra sets and images from https://github.com/e1ven/Robohash.git
namespace app\components\generators;

use yii\base\Widget;
use yii\helpers\Html;


class Robohash
{
  public $text;
  public $color;
  public $set;

  // WxH
  public $size;
  public $width;
  public $height;
  public $cache;
  public $image_dir;

  private $robodata;
  private $filename;

  private $hash_index     = 0,
    $hash_list      = [];

  const IMAGE_WIDTH       = 300,
    IMAGE_HEIGHT      = 300;


  public function __construct($text, $set = null, $color = null)
  {
    $this->image_dir =  \Yii::getAlias('@app/web/images/robohash/');
    $this->text = $text;
    $this->set = $set ?? \Yii::$app->sys->avatar_robohash_set;
    $this->color = $color;
    $this->width  = self::IMAGE_WIDTH;
    $this->height = self::IMAGE_WIDTH;

    $this->create_hashes($this->text);

    $this->set_set();

    $this->set_color();
    $this->robodata = $this->color === '' ? $this->set : sprintf("%s/%s", $this->set, $this->color);
  }

  // Hash chunks wrap around so sets with many layers never run out
  private function hash_at($index)
  {
    return $this->hash_list[$index % count($this->hash_list)];
  }

  private function create_hashes($text, $length = 11)
  {
    $hashes = str_split(hash('sha512', $text), $length);
    foreach ($hashes as $hash) {
      $this->hash_list[] = base_convert($hash, 16, 10);
    }
  }

  // Colored sets (like set1) have plain color folders instead of NNN#Layer folders
  private function set_color()
  {
    $colors = [];
    foreach (glob($this->image_dir . $this->set . '/*', GLOB_ONLYDIR) ?: [] as $dir) {
      if (strpos(basename($dir), '#') !== false) {
        $this->color = '';
        return;
      }
      $colors[] = basename($dir);
    }
    if ($colors === []) {
      $this->color = '';
    } elseif (!in_array($this->color, $colors, true)) {
      $this->color = $colors[$this->hash_at(0) % count($colors)];
    }
  }

  // Any folder under image_dir is a set; if the configured one is missing pick one by hash
  public function set_set()
  {
    if (is_string($this->set) && $this->set !== '' && is_dir($this->image_dir . $this->set))
      return;
    $sets = array_map('basename', glob($this->image_dir . '*', GLOB_ONLYDIR) ?: []);
    if ($sets === [])
      throw new \RuntimeException('No Robohash sets found in ' . $this->image_dir);
    natsort($sets);
    $sets = array_values($sets);
    $this->set = $sets[$this->hash_at(1) % count($sets)];
  }


  private function get_image_list()
  {
    $image_list = [];
    $temp = [];
    $dirs = glob($this->image_dir . "{$this->robodata}/*", GLOB_ONLYDIR) ?: [];
    foreach ($dirs as $dir) {
      if (!($files = glob("$dir/*.png"))) continue;
      $img_index = $this->hash_at($this->hash_index) % count($files);
      $this->hash_index++;
      $s = explode('#', $files[$img_index], 2);
      krsort($s);
      $temp[] = implode("|", $s);
    }

    sort($temp);

    foreach ($temp as $file) {
      $s = explode('|', $file, 2);
      krsort($s);
      $image_list[] = implode("#", $s);
    }
    return $image_list;
  }

  public function get_width_height()
  {
    return [$this->width, $this->height];
  }

  // Use GD as a fallback if host does not support ImageMagick.
  public function generate_image_gd($image_list)
  {
    // functions with alpha channel support
    $body = array_shift($image_list);
    $body = imagecreatefrompng($body);
    $body = $this->image_resize($body, self::IMAGE_WIDTH, self::IMAGE_HEIGHT);

    foreach ($image_list as $image_file) {
      $image = imagecreatefrompng($image_file);
      $image = $this->image_resize($image, self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
      $this->imagecopymerge_alpha($body, $image, 0, 0, 0, 0, imagesx($image), imagesy($image), 100);
      imagedestroy($image);
    }

    list($width, $height) = $this->get_width_height();

    $body = $this->image_resize($body, $width, $height);

    imagesavealpha($body, true);

    return $body;
  }

  public function generate_image()
  {
    $image_list = $this->get_image_list();
    $image = $this->generate_image_gd($image_list);
    return $image;
  }


  public function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
  {
    if (!isset($pct)) {
      return false;
    }
    $pct /= 100;
    // Get image width and height
    $w = imagesx($src_im);
    $h = imagesy($src_im);
    // Turn alpha blending off
    imagealphablending($src_im, false);
    // Find the most opaque pixel in the image (the one with the smallest alpha value)
    $minalpha = 127;
    for ($x = 0; $x < $w; $x++) {
      for ($y = 0; $y < $h; $y++) {
        $alpha = (imagecolorat($src_im, $x, $y) >> 24) & 0xFF;
        if ($alpha < $minalpha) {
          $minalpha = $alpha;
        }
      }
    }
    if ($minalpha === 127)
      return true;

    if (!$this->pixels_modify_alpha($src_im, $minalpha, $pct, $w, $h))
      return false;
    // The image copy
    imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
  }

  public function pixels_modify_alpha(&$src_im, $minalpha, $pct, $w, $h)
  {
    //loop through image pixels and modify alpha for each
    for ($x = 0; $x < $w; $x++) {
      for ($y = 0; $y < $h; $y++) {
        //get current alpha value (represents the TANSPARENCY!)
        $colorxy = imagecolorat($src_im, $x, $y);
        $alpha = ($colorxy >> 24) & 0xFF;
        //calculate new alpha
        if ($minalpha !== 127) {
          $alpha = 127 + 127 * $pct * ($alpha - 127) / (127 - $minalpha);
        } else {
          $alpha += 127 * $pct;
        }
        $alpha = (int) max(0, min(127, round($alpha)));
        //get the color index with new alpha
        $alphacolorxy = imagecolorallocatealpha($src_im, ($colorxy >> 16) & 0xFF, ($colorxy >> 8) & 0xFF, $colorxy & 0xFF, $alpha);
        //set pixel with the new color + opacity
        if (!imagesetpixel($src_im, $x, $y, $alphacolorxy)) {
          return false;
        }
      }
    }
    return true;
  }
  public function image_resize($src, $width, $height)
  {

    $img = imagecreatetruecolor($width, $height);

    imagecolortransparent($img, imagecolorallocatealpha($img, 0, 0, 0, 127));

    imagealphablending($img, false);
    imagesavealpha($img, true);

    imagecopyresampled($img, $src, 0, 0, 0, 0, $width, $height, imagesx($src), imagesy($src));

    imagealphablending($img, true);

    return $img;
  }
}
