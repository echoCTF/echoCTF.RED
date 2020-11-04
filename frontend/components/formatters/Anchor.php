<?php
namespace app\components\formatters;

use Yii;

class Anchor extends \yii\base\Model {

  public static function to($string) {
  $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $string)); // Removes special chars.
  }

}
