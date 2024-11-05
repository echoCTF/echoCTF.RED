<?php

namespace app\components\generators;

class AvatarGenerator
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

  private $hash_index     = 0,
    $hash_list      = [];

  const IMAGE_WIDTH       = 300,
    IMAGE_HEIGHT      = 300;
  private $generator;
  public function __construct($text, $set = null, $color = null)
  {
    $this->text = $text;
    $this->set = $set;
    $this->color = $color;
    $this->width  = self::IMAGE_WIDTH;
    $this->height = self::IMAGE_WIDTH;
    if (\Yii::$app->sys->avatar_generator === 'Identicon') {
      $this->generator = new \app\components\generators\Identicon($this->text, $this->set);
    } else {
      $this->generator = new \app\components\generators\Robohash($this->text, $this->set);
    }
  }

  public function generate_image()
  {
    return $this->generator->generate_image();
  }
}
