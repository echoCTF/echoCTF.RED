<?php
namespace app\components\generators;
class Identicon {
  public $text;
  public $color;
  public $set;
  public $width;
  public $height;
  const IMAGE_WIDTH       = 300,
    IMAGE_HEIGHT      = 300;
  private $model;
  public function __construct($text, $set = null, $color = null)
  {
    $this->text = $text;
    $this->set = $set;
    $this->color = $color;
    $this->width  = self::IMAGE_WIDTH;
    $this->height = self::IMAGE_WIDTH;
    $this->model=new \Identicon\Identicon();
  }

  public function generate_image()
  {
    return $this->model->getImageResource($this->text,self::IMAGE_WIDTH,$this->color);
  }

}